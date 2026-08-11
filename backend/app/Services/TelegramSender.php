<?php

namespace App\Services;

use App\Enums\NotificationKind;
use App\Models\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * The single door out to Telegram.
 *
 * Nothing calls the Bot API directly. Every message becomes a `notifications`
 * row first, which is what makes the history, the retry button and idempotency
 * one mechanism instead of three.
 */
class TelegramSender
{
    private const ENDPOINT = 'https://api.telegram.org/bot%s/%s';

    /**
     * Queue a message by recording it. Delivery is a separate step, so the
     * caller is never blocked by a slow API.
     *
     * @param  array<string, mixed>  $keyboard  Inline keyboard, if the message has one.
     */
    public function queue(
        int $userId,
        NotificationKind $kind,
        string $title,
        ?string $body = null,
        ?int $planId = null,
        int $sequence = 0,
    ): Notification {
        return Notification::query()->create([
            'user_id' => $userId,
            'plan_id' => $planId,
            'kind' => $kind,
            'sequence' => $sequence,
            'title' => $title,
            'body' => $body,
            'chat_id' => config('services.telegram.chat_id') ?: null,
        ]);
    }

    /**
     * Attempt delivery and record the outcome on the row itself.
     *
     * @param  array<string, mixed>|null  $replyMarkup
     */
    public function deliver(Notification $notification, ?array $replyMarkup = null): bool
    {
        $token = config('services.telegram.token');
        $chatId = $notification->chat_id ?: config('services.telegram.chat_id');

        if (! $token || ! $chatId) {
            $notification->markFailed('Telegram is not configured.');

            return false;
        }

        $text = $notification->body
            ? '<b>' . e($notification->title) . "</b>\n\n" . e($notification->body)
            : '<b>' . e($notification->title) . '</b>';

        try {
            $response = Http::timeout(15)->asForm()->post(
                sprintf(self::ENDPOINT, $token, 'sendMessage'),
                array_filter([
                    'chat_id' => $chatId,
                    'text' => $text,
                    'parse_mode' => 'HTML',
                    'reply_markup' => $replyMarkup ? json_encode($replyMarkup) : null,
                ])
            );

            if ($response->successful()) {
                $notification->markSent($response->json('result.message_id'));

                return true;
            }

            $notification->markFailed('Telegram API: ' . $response->status() . ' ' . $response->json('description', ''));
        } catch (Throwable $e) {
            $notification->markFailed($e->getMessage());
            Log::warning('Telegram delivery failed', ['notification' => $notification->id, 'error' => $e->getMessage()]);
        }

        return false;
    }
}
