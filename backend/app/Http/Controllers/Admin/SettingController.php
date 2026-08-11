<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\SiteContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * General site settings — the `personalInfo` block of `/api/content`.
 *
 * The social networks are a fixed set rather than a free-form list: the
 * frontend reads them by name (`personalInfo.social.telegram.link`), so a
 * removed or renamed key would break the contact page rather than simply
 * hide a link.
 */
class SettingController extends Controller
{
    private const SOCIAL = [
        'email'     => ['label' => 'Email', 'placeholder' => 'mailto:siz@example.com'],
        'phone'     => ['label' => 'Phone', 'placeholder' => 'tel:+998901234567'],
        'github'    => ['label' => 'GitHub', 'placeholder' => 'https://github.com/…'],
        'linkedin'  => ['label' => 'LinkedIn', 'placeholder' => 'https://linkedin.com/in/…'],
        'telegram'  => ['label' => 'Telegram', 'placeholder' => 'https://t.me/…'],
        'instagram' => ['label' => 'Instagram', 'placeholder' => 'https://instagram.com/…'],
    ];

    public function index()
    {
        abort_unless(Auth::user()?->can('settings.view'), 403);

        $settings = SiteSetting::pluck('value', 'key');

        return view('admin.settings.index', [
            'name'     => (array) ($settings['name'] ?? []),
            'location' => (array) ($settings['location'] ?? []),
            'social'   => (array) ($settings['social'] ?? []),
            'networks' => self::SOCIAL,
        ]);
    }

    public function update(Request $request)
    {
        abort_unless(Auth::user()?->can('settings.edit'), 403);

        $locales = array_keys(SiteContent::LOCALES);
        $primary = $locales[0];

        $rules = [
            "name.{$primary}"     => ['required', 'string', 'max:255'],
            "location.{$primary}" => ['required', 'string', 'max:255'],
        ];

        foreach ($locales as $locale) {
            $rules["name.{$locale}"]     ??= ['nullable', 'string', 'max:255'];
            $rules["location.{$locale}"] ??= ['nullable', 'string', 'max:255'];
        }

        foreach (array_keys(self::SOCIAL) as $network) {
            $rules["social.{$network}.link"]  = ['required', 'string', 'max:255'];
            $rules["social.{$network}.label"] = ['required', 'string', 'max:255'];
        }

        $data = $request->validate($rules);

        $this->save('name', $this->fillLocales($data['name'], $locales, $primary));
        $this->save('location', $this->fillLocales($data['location'], $locales, $primary));

        // Rebuild from the known list so an injected extra key cannot land in
        // the payload the site consumes.
        $social = [];
        foreach (array_keys(self::SOCIAL) as $network) {
            $social[$network] = [
                'link'  => trim($data['social'][$network]['link']),
                'label' => trim($data['social'][$network]['label']),
            ];
        }
        $this->save('social', $social);

        SiteContent::flush();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings saved.');
    }

    private function save(string $key, array $value): void
    {
        SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /** Fall back to the primary language for any translation left blank. */
    private function fillLocales(array $values, array $locales, string $primary): array
    {
        $fallback = trim((string) ($values[$primary] ?? ''));

        $filled = [];

        foreach ($locales as $locale) {
            $value           = trim((string) ($values[$locale] ?? ''));
            $filled[$locale] = $value !== '' ? $value : $fallback;
        }

        return $filled;
    }
}
