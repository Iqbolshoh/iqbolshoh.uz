<?php

namespace App\Support;

/**
 * The technology catalogue behind the admin pickers and the site's badges.
 *
 * Names are the source of truth: they are stored verbatim in the `tech` JSON
 * columns, so a record written before a technology joined the catalogue keeps
 * working — it simply renders with the neutral fallback until an entry exists.
 */
class SiteTech
{
    /** Badge colour for a technology the catalogue does not know. */
    public const FALLBACK_COLOR = '#8B95A5';

    /** @return array<string, array{icon: ?string, color: string}> */
    public static function catalog(): array
    {
        return config('technologies', []);
    }

    /** @return list<string> */
    public static function names(): array
    {
        return array_keys(self::catalog());
    }

    /** Public URL of a technology's icon, or null when it has none. */
    public static function iconUrl(string $name): ?string
    {
        $icon = self::catalog()[$name]['icon'] ?? null;

        return $icon ? "/media/tech/{$icon}.svg" : null;
    }

    public static function color(string $name): string
    {
        return self::catalog()[$name]['color'] ?? self::FALLBACK_COLOR;
    }

    /**
     * The catalogue in the shape the React site consumes: a name-keyed map of
     * ready-to-use icon URLs and colours, so a badge needs no lookup table of
     * its own.
     *
     * @param  list<string>  $extraNames  Names in use that predate the catalogue.
     * @return array<string, array{icon: ?string, color: string}>
     */
    public static function map(array $extraNames = []): array
    {
        $names = array_unique([...self::names(), ...$extraNames]);
        sort($names);

        $map = [];

        foreach ($names as $name) {
            $map[$name] = [
                'icon'  => self::iconUrl($name),
                'color' => self::color($name),
            ];
        }

        return $map;
    }
}
