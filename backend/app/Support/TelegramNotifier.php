<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Telegram'ga bildirishnoma yuboradi.
 *
 * Yuborilmasa ham chaqiruvchi kod to'xtamaydi — xabar allaqachon bazaga
 * yozilgan bo'ladi, Telegram faqat qo'shimcha xabardor qilish vositasi.
 */
class TelegramNotifier
{
    /**
     * @param  array<string, string|null>  $fields  "Sarlavha" => qiymat
     */
    public function send(string $title, array $fields): void
    {
        $token  = config('services.telegram.token');
        $chatId = config('services.telegram.chat_id');

        if (! $token || ! $chatId) {
            return;
        }

        $lines = ['<b>' . e($title) . '</b>', ''];

        foreach ($fields as $label => $value) {
            $lines[] = '<b>' . e($label) . ':</b> ' . e((string) ($value ?: '—'));
        }

        try {
            $response = Http::timeout(10)
                ->asForm()
                ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id'    => $chatId,
                    'text'       => implode("\n", $lines),
                    'parse_mode' => 'HTML',
                ]);

            if ($response->failed()) {
                Log::warning('Telegram yuborilmadi', ['status' => $response->status()]);
            }
        } catch (\Throwable $e) {
            Log::warning('Telegram xatosi: ' . $e->getMessage());
        }
    }
}
