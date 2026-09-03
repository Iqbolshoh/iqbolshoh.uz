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

    /**
     * Refusals that mean "there was nothing to do", not "something broke".
     *
     * Telegram rejects an edit that would leave the message exactly as it is,
     * which happens on every refresh of a screen that has not changed and on
     * every second press of the button already showing. Nothing is wrong: the
     * screen is current. Logging it as an API error buries the real ones.
     */
    private const BENIGN = ['message is not modified'];

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

    /**
     * The buttons that sit under the text box, always in reach.
     *
     * Different in kind from an inline keyboard: these are not attached to a
     * message and cannot be edited away, and pressing one sends its own label
     * as an ordinary message. Everything put here therefore has to be
     * recognised again on the way in — see TelegramBot::shortcut().
     *
     * @param  list<list<string>>  $rows
     * @return array<string, mixed>
     */
    public static function replyKeyboard(array $rows): array
    {
        return [
            'keyboard' => array_map(
                fn (array $row): array => array_map(fn (string $label): array => ['text' => $label], $row),
                $rows
            ),
            'resize_keyboard' => true,
            'is_persistent' => true,
        ];
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

    /**
     * The "/" menu, for one language.
     *
     * `$languageCode` is Telegram's own code, not this app's: it knows Tajik
     * as "tg". An empty string means the default scope — the list every client
     * whose language has no list of its own falls back to, which is why it can
     * never be skipped.
     *
     * @param  list<array{command: string, description: string}>  $commands
     */
    public function setMyCommands(array $commands, string $languageCode = ''): ?Response
    {
        return $this->call('setMyCommands', array_filter([
            'commands' => json_encode(array_values($commands)),
            'language_code' => $languageCode,
        ]));
    }

    public function setMyName(string $name, string $languageCode = ''): ?Response
    {
        return $this->call('setMyName', array_filter([
            'name' => $name,
            'language_code' => $languageCode,
        ]));
    }

    /** The text on an empty chat, before the first message. Plain text only. */
    public function setMyDescription(string $description, string $languageCode = ''): ?Response
    {
        return $this->call('setMyDescription', array_filter([
            'description' => $description,
            'language_code' => $languageCode,
        ]));
    }

    /** The line under the bot's name in search and on its profile. */
    public function setMyShortDescription(string $description, string $languageCode = ''): ?Response
    {
        return $this->call('setMyShortDescription', array_filter([
            'short_description' => $description,
            'language_code' => $languageCode,
        ]));
    }

    /**
     * The bot's avatar.
     *
     * The odd one out: it is not stored per language, and the file has to be
     * attached rather than form-encoded, so it does not go through `call()`.
     * `photo` is a JSON descriptor pointing at the attachment by name.
     */
    public function setMyProfilePhoto(string $path): ?Response
    {
        if (! $this->isConfigured()) {
            Log::warning('Telegram called without a token', ['method' => 'setMyProfilePhoto']);

            return null;
        }

        try {
            $response = Http::timeout(self::CALL_TIMEOUT)
                ->attach('avatar', file_get_contents($path), basename($path))
                ->post($this->url('setMyProfilePhoto'), [
                    'photo' => json_encode(['type' => 'static', 'photo' => 'attach://avatar']),
                ]);

            if ($response->failed()) {
                Log::warning('Telegram API error', [
                    'method' => 'setMyProfilePhoto',
                    'status' => $response->status(),
                    'description' => $response->json('description'),
                ]);
            }

            return $response;
        } catch (Throwable $e) {
            Log::warning('Telegram request failed', ['method' => 'setMyProfilePhoto', 'error' => $e->getMessage()]);

            return null;
        }
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

            if ($response->failed() && ! $this->benign($response->json('description'))) {
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

    private function benign(?string $description): bool
    {
        foreach (self::BENIGN as $needle) {
            if ($description !== null && str_contains($description, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function url(string $method): string
    {
        return sprintf(self::ENDPOINT, config('services.telegram.token'), $method);
    }
}
