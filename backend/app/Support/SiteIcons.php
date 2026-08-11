<?php

namespace App\Support;

/**
 * Icon names the frontend knows how to render.
 *
 * This list must mirror `src/lib/icons.ts`. Any name missing there falls back
 * to the `Code` icon on the site, so the admin form offers these as a fixed
 * choice rather than a free-text field.
 */
class SiteIcons
{
    public const NAMES = [
        'Award',
        'BookOpen',
        'BookOpenCheck',
        'Bot',
        'Brush',
        'Code',
        'Code2',
        'Compass',
        'Database',
        'FileCode',
        'FileText',
        'GraduationCap',
        'HeartHandshake',
        'Layout',
        'Lightbulb',
        'MessageCircle',
        'Monitor',
        'Network',
        'Package',
        'Palette',
        'Rocket',
        'Server',
        'ShieldCheck',
        'Star',
        'Terminal',
        'Users',
    ];

    /** Turn a lucide name into its blade component slug: "BookOpen" → "book-open". */
    public static function slug(string $name): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]|(?<=[a-z])(?=\d)/', '-$0', $name));
    }

    /**
     * Blade component name for an icon, mirroring the site's own fallback:
     * anything unknown renders as `Code` instead of throwing.
     */
    public static function component(?string $name): string
    {
        return 'lucide-' . self::slug(in_array($name, self::NAMES, true) ? $name : 'Code');
    }
}
