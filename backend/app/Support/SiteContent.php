<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Shared helpers for the public site content.
 */
class SiteContent
{
    /** Cache key holding the whole `/api/content` payload. */
    public const CACHE_KEY = 'site.content';

    /**
     * Languages the site text is stored in.
     *
     * The first entry is the primary one: it is the tab the admin form opens
     * on, the language the listings show, and the one every other language is
     * filled from when left blank. The panel runs in English, so English leads
     * — the site itself still serves Uzbek first, which is an unrelated setting
     * in the React app's i18n config.
     */
    public const LOCALES = [
        'en' => 'English',
        'uz' => "O'zbekcha",
        'ru' => 'Русский',
        'tj' => 'Тоҷикӣ',
    ];

    /**
     * Drop the cached payload after an edit. Without this the site keeps
     * serving stale content for up to a minute, which reads to the editor as
     * "I saved it and nothing changed".
     */
    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
