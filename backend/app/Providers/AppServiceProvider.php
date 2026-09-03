<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Foundation\Events\LocaleUpdated;
use Illuminate\Support\Facades\Event;
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
        // Carbon does not speak this app's locale codes.
        //
        // Plain "uz" is Cyrillic Uzbek to Carbon, so a bot answering in Latin
        // Uzbek printed "Пайшанба, 3 сентябр" as the heading over a screen of
        // Latin text; and "tj" is not a Carbon locale at all, which left every
        // Tajik date in English. Nothing errors either way — the dates simply
        // come out in the wrong alphabet.
        //
        // The listener catches every later change, and the call under it the
        // locale the request already booted with.
        Event::listen(LocaleUpdated::class, fn (LocaleUpdated $event) => Carbon::setLocale(self::carbonLocale($event->locale)));

        Carbon::setLocale(self::carbonLocale($this->app->getLocale()));

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

    /** This app's locale code => the one Carbon files that language under. */
    private static function carbonLocale(string $locale): string
    {
        return match ($locale) {
            'uz' => 'uz_Latn',
            'tj' => 'tg',
            default => $locale,
        };
    }
}
