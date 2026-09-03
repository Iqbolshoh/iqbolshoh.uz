<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Every Telegram call goes over IPv4, and only Telegram's.
        //
        // `api.telegram.org` answers on both IPv6 and IPv4, getaddrinfo hands
        // back the IPv6 address first, and this server's IPv6 route to Telegram
        // drops roughly one TLS handshake in five. The TCP connect succeeds and
        // the handshake then hangs, so a stalled call does not fail fast — it
        // burns the entire timeout. The symptom is never an error: it is a bot
        // that answers instantly most of the time and hangs for half a minute
        // the rest of it.
        //
        // This sits here rather than on each call site because TelegramClient
        // alone has half a dozen of those, and the next one somebody writes
        // would be back to hanging. The host check keeps it to Telegram: no
        // other host has a bad IPv6 route from this box, and pinning every
        // outbound request would be a far larger change than the problem asks
        // for.
        Http::globalMiddleware(fn (callable $handler) => function ($request, array $options) use ($handler) {
            if ($request->getUri()->getHost() === 'api.telegram.org') {
                $options['curl'][CURLOPT_IPRESOLVE] = CURL_IPRESOLVE_V4;
            }

            return $handler($request, $options);
        });
    }
}
