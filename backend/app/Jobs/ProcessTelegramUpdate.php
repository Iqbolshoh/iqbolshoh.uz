<?php

namespace App\Jobs;

use App\Services\TelegramBot;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * One Telegram update, handled off the request.
 *
 * Retries are deliberately few: a button pressed once should not fire three
 * times because the Bot API was briefly slow.
 */
class ProcessTelegramUpdate implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public int $timeout = 60;

    /** @param  array<string, mixed>  $update */
    public function __construct(private readonly array $update) {}

    public function handle(TelegramBot $bot): void
    {
        if (isset($this->update['callback_query'])) {
            $bot->handleCallback($this->update['callback_query']);

            return;
        }

        if (isset($this->update['message'])) {
            $bot->handleMessage($this->update['message']);
        }
    }
}
