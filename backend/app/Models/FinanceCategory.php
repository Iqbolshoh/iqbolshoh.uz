<?php

namespace App\Models;

use App\Enums\TransactionKind;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * A bucket money falls into: food, transport, rent, salary.
 *
 * A category seeded by the installer carries a `key`, and the bot renders it
 * through the translation files so the same row reads "Ovqat", "Еда", "Food"
 * or "Хӯрок" depending on who is looking. A category the owner types in later
 * has no key and is shown exactly as typed — inventing a translation for it
 * would be a guess.
 */
class FinanceCategory extends Model
{
    protected $fillable = [
        'user_id',
        'kind',
        'key',
        'name',
        'icon',
        'color',
        'keywords',
        'monthly_limit',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'kind' => TransactionKind::class,
            'monthly_limit' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'category_id');
    }

    public function scopeOfKind(Builder $query, TransactionKind $kind): Builder
    {
        return $query->where('kind', $kind->value);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * What to call this category in the given language.
     *
     * `__()` falls back to the fallback locale when a key is missing, which
     * would quietly show English inside a Tajik sentence — so the seeded key
     * is only trusted when the line actually exists for that locale.
     */
    public function displayName(?string $locale = null): string
    {
        if ($this->key === null) {
            return $this->name;
        }

        $line = "finance.categories.{$this->key}";

        return \Illuminate\Support\Facades\Lang::has($line, $locale, false)
            ? __($line, [], $locale)
            : $this->name;
    }

    /** Name with its icon, for a button or a list row. */
    public function label(?string $locale = null): string
    {
        return trim(($this->icon ?? '') . ' ' . $this->displayName($locale));
    }

    /**
     * The words free text is matched against, always including the category's
     * own name so a newly added category is reachable from the bot without the
     * owner having to think about keywords at all.
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
