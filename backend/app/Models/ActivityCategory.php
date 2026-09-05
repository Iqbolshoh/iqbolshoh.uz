<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * A way of spending time: sleep, work, study, the road.
 *
 * The same shape as a finance category on purpose — seeded rows carry a `key`
 * and are translated, hand-made ones are shown as typed — because the two
 * modules are read side by side and nothing is gained by them behaving
 * differently.
 */
class ActivityCategory extends Model
{
    protected $fillable = [
        'user_id',
        'key',
        'name',
        'icon',
        'color',
        'keywords',
        'daily_target_minutes',
        'is_good',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'daily_target_minutes' => 'integer',
            'is_good' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(ActivityEntry::class, 'category_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * What to call this activity in the given language.
     *
     * `__()` falls back to the fallback locale when a line is missing, which
     * would quietly show English inside a Tajik sentence — so the seeded key is
     * only trusted when the line actually exists for that locale.
     */
    public function displayName(?string $locale = null): string
    {
        if ($this->key === null) {
            return $this->name;
        }

        $line = "activity.categories.{$this->key}";

        return \Illuminate\Support\Facades\Lang::has($line, $locale, false)
            ? __($line, [], $locale)
            : $this->name;
    }

    public function label(?string $locale = null): string
    {
        return trim(($this->icon ?? '') . ' ' . $this->displayName($locale));
    }

    /**
     * The words free text is matched against, always including the activity's
     * own name so one the owner adds is reachable from the bot without them
     * having to think about keywords at all.
     *
     * @return list<string>
     */
    public function matchWords(): array
    {
        $words = Str::of((string) $this->keywords)
            ->lower()
            ->explode(',')
            ->map(fn (string $word) => trim($word))
            ->filter()
            ->all();

        $words[] = Str::lower($this->name);

        return array_values(array_unique($words));
    }
}
