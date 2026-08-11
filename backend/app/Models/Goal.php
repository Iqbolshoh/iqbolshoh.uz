<?php

namespace App\Models;

use App\Enums\GoalStatus;
use App\Enums\PlanStatus;
use App\Enums\Priority;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A goal for one month: "30 hours of English", "ship the Templates.uz redesign".
 *
 * Daily plans hang off it, and its progress is the share of those plans that
 * were completed. `month` is always stored as the first day of the month, so
 * "this month's goals" is an indexed date lookup rather than string matching.
 */
class Goal extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'title',
        'description',
        'target',
        'priority',
        'status',
        'color',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'date',
            'priority' => Priority::class,
            'status' => GoalStatus::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    public function scopeForMonth(Builder $query, CarbonInterface|string $month): void
    {
        $query->whereDate('month', Carbon::parse($month)->startOfMonth());
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('status', GoalStatus::Active);
    }

    /**
     * Share of this goal's plans that were completed, 0–100.
     *
     * Pending plans are counted in the denominator on purpose: a goal halfway
     * through the month with nothing done yet should read as behind, not as
     * having no data.
     */
    public function progress(): int
    {
        $total = $this->plans()->count();

        if ($total === 0) {
            return 0;
        }

        $completed = $this->plans()->where('status', PlanStatus::Completed)->count();

        return (int) round($completed / $total * 100);
    }
}
