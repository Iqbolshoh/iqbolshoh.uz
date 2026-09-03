<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The forecast for one month, frozen at the moment it was produced.
 *
 * Storing it rather than recomputing on each page load matters for trust: a
 * prediction that quietly changes every time it is opened is not a prediction.
 */
class ForecastReport extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'source_plans',
        'source_completed',
        'raw_rate',
        'true_rate',
        'confidence',
        'projection',
        'segments',
        'recommendations',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            // Pinned, not a bare `date`: that cast writes back a midnight
            // timestamp, which MySQL truncates into its DATE column and SQLite
            // keeps whole — so an exact match on the column finds nothing and a
            // range misses its own last day, in the test suite only.
            'month' => 'date:Y-m-d',
            'raw_rate' => 'float',
            'true_rate' => 'float',
            'projection' => 'array',
            'segments' => 'array',
            'recommendations' => 'array',
            'generated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
