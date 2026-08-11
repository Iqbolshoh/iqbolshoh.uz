<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessTelegramUpdate;
use App\Services\TelegramClient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * The bot's front door.
 *
 * Two things happen here and nothing else. The button is acknowledged straight
 * away, because Telegram keeps it spinning until it is — and the real work goes
 * to the queue, because Telegram re-sends any update it does not get a fast 200
 * for, which would run the same action twice.
 */
class WebhookController extends Controller
{
    public function __construct(private readonly TelegramClient $client) {}

    public function __invoke(Request $request): Response
    {
        $secret = config('services.telegram.webhook_secret');

        if ($secret && $request->header('X-Telegram-Bot-Api-Secret-Token') !== $secret) {
            return response('', 403);
        }

        $update = $request->all();

        if ($callbackQueryId = data_get($update, 'callback_query.id')) {
            $this->client->acknowledge($callbackQueryId);
        }

        ProcessTelegramUpdate::dispatch($update);

        return response('', 200);
    }
}
