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

    /** Languages the site text is stored in. The first one is the admin form's default tab. */
    public const LOCALES = [
        'uz' => "O'zbekcha",
        'en' => 'English',
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
