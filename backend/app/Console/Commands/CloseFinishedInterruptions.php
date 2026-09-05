<?php

namespace App\Console\Commands;

use App\Models\Interruption;
use App\Services\ActivityService;
use Illuminate\Console\Command;

/**
 * Close the interruptions nobody came back to, and keep their hours.
 *
 * "I'm busy for two hours" is answered by a button when the owner remembers to
 * press it, and forgotten otherwise. Left alone, those rows stay open forever
 * and the two hours they represent never reach the time log — which is exactly
 * the shape of day the log is meant to explain.
 *
 * So once the stated end has passed, the interruption is closed at the time it
 * said it would end. That is an estimate, and an honest one: it is the number
 * the owner themselves gave.
 */
class CloseFinishedInterruptions extends Command
{
    protected $signature = 'interruptions:close';

    protected $description = 'Close interruptions whose stated end has passed, and log the time they took';

    public function handle(ActivityService $activities): int
    {
        $finished = Interruption::query()
            ->whereNull('ended_at')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->with('user')
            ->get();

        $logged = 0;

        foreach ($finished as $interruption) {
            $interruption->update(['ended_at' => $interruption->ends_at]);

            if ($activities->recordInterruption($interruption) !== null) {
                $logged++;
            }
        }

        $this->info("Closed {$finished->count()} interruptions, logged {$logged}.");

        return self::SUCCESS;
    }
}
