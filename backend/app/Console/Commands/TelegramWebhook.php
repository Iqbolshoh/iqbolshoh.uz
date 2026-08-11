<?php

namespace App\Console\Commands;

use App\Services\TelegramClient;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Point Telegram at this installation, or unhook it again.
 *
 * The secret is generated here rather than typed by hand, then written to .env
 * so the webhook controller can compare against it.
 */
class TelegramWebhook extends Command
{
    protected $signature = 'telegram:webhook {action=set : set, delete or info}';

    protected $description = 'Register, remove or inspect the Telegram webhook';

    public function handle(TelegramClient $client): int
    {
        if (! $client->isConfigured()) {
            $this->error('TELEGRAM_BOT_TOKEN is not set.');

            return self::FAILURE;
        }

        return match ($this->argument('action')) {
            'set' => $this->set($client),
            'delete' => $this->delete($client),
            'info' => $this->info_($client),
            default => $this->invalid(),
        };
    }

    private function set(TelegramClient $client): int
    {
        $secret = config('services.telegram.webhook_secret') ?: Str::random(48);
        $url = rtrim(config('app.url'), '/') . '/telegram/webhook';

        $response = $client->setWebhook($url, $secret);

        if ($response?->json('ok') !== true) {
            $this->error('Telegram refused the webhook: ' . $response?->json('description'));

            return self::FAILURE;
        }

        $this->info("Webhook set to {$url}");

        if (! config('services.telegram.webhook_secret')) {
            $this->warn('Add this to .env, then run config:clear:');
            $this->line("TELEGRAM_WEBHOOK_SECRET=\"{$secret}\"");
        }

        return self::SUCCESS;
    }

    private function delete(TelegramClient $client): int
    {
        $client->deleteWebhook();
        $this->info('Webhook removed.');

        return self::SUCCESS;
    }

    private function info_(TelegramClient $client): int
    {
        $me = $client->getMe();
        $this->line('Bot: @' . $me?->json('result.username'));

        return self::SUCCESS;
    }

    private function invalid(): int
    {
        $this->error('Use: set, delete or info');

        return self::FAILURE;
    }
}
