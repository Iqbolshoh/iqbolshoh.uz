<?php

namespace App\Models;

use App\Enums\ActivitySource;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A stretch of time, already spent.
 *
 * Only a duration, never a start and an end. What gets written down is "three
 * hours of work", usually well after the fact; a pair of timestamps would
 * force an invented start time and then invite overlap questions the data
 * cannot honestly answer.
 */
class ActivityEntry extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'minutes',
        'date',
        'note',
        'source',
        'interruption_id',
    ];

    protected function casts(): array
    {
        return [
            // Pinned to the date format for the same reason a transaction's is:
            // a bare `date` cast writes back a midnight timestamp, which SQLite
            // stores whole and MySQL truncates — so a `whereBetween` on the day
            // sums to zero in the test suite and to the right number in
            // production. See [[reference-date-cast-writes-midnight]].
            'date' => 'date:Y-m-d',
            'minutes' => 'integer',
            'source' => ActivitySource::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ActivityCategory::class, 'category_id');
    }

    public function interruption(): BelongsTo
    {
        return $this->belongsTo(Interruption::class);
    }

    /** Inclusive on both ends: a month range must contain its last day. */
    public function scopeBetween(Builder $query, CarbonInterface $from, CarbonInterface $to): Builder
    {
        return $query->whereBetween('date', [$from->toDateString(), $to->toDateString()]);
    }

    /**
     * Minutes as a person says them: "8 soat", "2 soat 30 daq", "45 daq".
     *
     * The hour is dropped when there is none rather than printed as "0 soat",
     * and the minutes are dropped when they are zero — "8 soat 0 daq" is how a
     * stopwatch talks, not how anybody reports their day.
     */
    public static function duration(int $minutes, ?string $locale = null): string
    {
        $hours = intdiv($minutes, 60);
        $rest = $minutes % 60;

        $parts = [];

        if ($hours > 0) {
            $parts[] = $hours . ' ' . __('activity.unit.hour', [], $locale);
        }

        if ($rest > 0 || $hours === 0) {
            $parts[] = $rest . ' ' . __('activity.unit.minute', [], $locale);
        }

        return implode(' ', $parts);
    }

    public function formattedDuration(?string $locale = null): string
    {
        return self::duration($this->minutes, $locale);
    }
}
