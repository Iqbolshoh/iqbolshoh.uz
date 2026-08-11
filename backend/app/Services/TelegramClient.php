<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * A thin wrapper over the Bot API.
 *
 * The one rule that matters for how the bot feels: `answerCallbackQuery` must
 * go out the moment a button is pressed. Telegram spins that button until the
 * callback is answered, so anything slower than an immediate acknowledgement
 * reads to the person tapping it as a frozen bot — no matter how fast the real
 * work is.
 */
class TelegramClient
{
    private const ENDPOINT = 'https://api.telegram.org/bot%s/%s';

    /** Deliberately short: a hung acknowledgement is worse than a missed one. */
    private const ACK_TIMEOUT = 3;

    private const CALL_TIMEOUT = 15;

    public function isConfigured(): bool
    {
        return (bool) config('services.telegram.token');
    }

    /**
     * Stop the button's spinner. Called before any work is done, and never
     * allowed to throw — a failed acknowledgement must not abort the action the
     * person actually asked for.
     */
    public function acknowledge(string $callbackQueryId, ?string $text = null): void
    {
        try {
            Http::timeout(self::ACK_TIMEOUT)->asForm()->post(
                $this->url('answerCallbackQuery'),
                array_filter([
                    'callback_query_id' => $callbackQueryId,
                    'text' => $text,
                ])
            );
        } catch (Throwable $e) {
            Log::debug('Telegram acknowledgement failed', ['error' => $e->getMessage()]);
        }
    }

    /** @param  array<string, mixed>|null  $keyboard */
    public function sendMessage(int|string $chatId, string $text, ?array $keyboard = null): ?Response
    {
        return $this->call('sendMessage', array_filter([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $keyboard ? json_encode($keyboard) : null,
        ]));
    }

    /**
     * Replace the message a button lives on. Used instead of sending a new one
     * so a plan's card updates in place rather than pushing the chat down.
     *
     * @param  array<string, mixed>|null  $keyboard
     */
    public function editMessage(int|string $chatId, int $messageId, string $text, ?array $keyboard = null): ?Response
    {
        return $this->call('editMessageText', array_filter([
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => $keyboard ? json_encode($keyboard) : null,
        ]));
    }

    /** @param  list<list<array<string, string>>>  $rows */
    public static function keyboard(array $rows): array
    {
        return ['inline_keyboard' => $rows];
    }

    /** One inline button. Callback data must stay under Telegram's 64-byte limit. */
    public static function button(string $label, string $callbackData): array
    {
        return ['text' => $label, 'callback_data' => $callbackData];
    }

    public function setWebhook(string $url, string $secret): ?Response
    {
        return $this->call('setWebhook', [
            'url' => $url,
            'secret_token' => $secret,
            'allowed_updates' => json_encode(['message', 'callback_query']),
            'drop_pending_updates' => true,
        ]);
    }

    public function deleteWebhook(): ?Response
    {
        return $this->call('deleteWebhook', ['drop_pending_updates' => true]);
    }

    public function getMe(): ?Response
    {
        return $this->call('getMe', []);
    }

    /** @param  array<string, mixed>  $payload */
    private function call(string $method, array $payload): ?Response
    {
        if (! $this->isConfigured()) {
            Log::warning('Telegram called without a token', ['method' => $method]);

            return null;
        }

        try {
            $response = Http::timeout(self::CALL_TIMEOUT)->asForm()->post($this->url($method), $payload);

            if ($response->failed()) {
                Log::warning('Telegram API error', [
                    'method' => $method,
                    'status' => $response->status(),
                    'description' => $response->json('description'),
                ]);
            }

            return $response;
        } catch (Throwable $e) {
            Log::warning('Telegram request failed', ['method' => $method, 'error' => $e->getMessage()]);

            return null;
        }
    }

    private function url(string $method): string
    {
        return sprintf(self::ENDPOINT, config('services.telegram.token'), $method);
    }
}
