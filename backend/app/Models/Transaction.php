<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\TransactionKind;
use App\Enums\TransactionSource;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One movement of money.
 *
 * `amount` is always positive; `kind` carries the direction. Storing an expense
 * as a negative number would make every sum look right and every average,
 * maximum and "biggest expense" query quietly wrong.
 */
class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'kind',
        'amount',
        'date',
        'time',
        'note',
        'method',
        'source',
    ];

    protected function casts(): array
    {
        return [
            // The format matters, not just the type. A bare `date` cast writes
            // back "2026-09-03 00:00:00", which MySQL truncates into its DATE
            // column and SQLite stores whole — so a `whereBetween` on the day
            // silently sums to zero in the test suite and to the right number
            // in production. Pinning the format makes both agree.
            'date' => 'date:Y-m-d',
            'kind' => TransactionKind::class,
            'method' => PaymentMethod::class,
            'source' => TransactionSource::class,
            'amount' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FinanceCategory::class, 'category_id');
    }

    public function scopeOfKind(Builder $query, TransactionKind $kind): Builder
    {
        return $query->where('kind', $kind->value);
    }

    /** Inclusive on both ends: a month range must contain its last day. */
    public function scopeBetween(Builder $query, CarbonInterface $from, CarbonInterface $to): Builder
    {
        return $query->whereBetween('date', [$from->toDateString(), $to->toDateString()]);
    }

    /**
     * Money as the owner reads it: "25 000 so'm".
     *
     * A non-breaking thin space groups the digits, because a normal space lets
     * a narrow phone break the number across two lines. The currency word is
     * translated so the bot can say "сум" or "сӯм" without the number ever
     * being reformatted.
     */
    public static function money(int $amount, ?string $locale = null): string
    {
        return number_format($amount, 0, '.', "\u{202F}") . ' ' . __('finance.currency', [], $locale);
    }

    public function formattedAmount(?string $locale = null): string
    {
        return ($this->kind === TransactionKind::Income ? '+' : '−') . ' ' . self::money($this->amount, $locale);
    }

    /** The wall-clock moment, or just the date when no time was recorded. */
    public function occurredLabel(): string
    {
        $date = $this->date->format('d.m.Y');

        return $this->time === null ? $date : $date . ' ' . substr((string) $this->time, 0, 5);
    }
}
