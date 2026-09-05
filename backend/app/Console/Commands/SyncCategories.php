<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ActivityService;
use App\Services\FinanceService;
use Illuminate\Console\Command;

/**
 * Bring every account's categories in line with the shipped catalogues.
 *
 * One command for both modules rather than two, because they are always out of
 * date for the same reason — a release changed the list — and a deploy that
 * runs half of them leaves the bot filing "kafe" under Groceries or "dars"
 * nowhere at all.
 *
 * The panel has buttons for this too, but a deploy should not wait for
 * somebody to log in and press them. It belongs next to `migrate --force`.
 */
class SyncCategories extends Command
{
    protected $signature = 'categories:sync {--user= : Only this user id}';

    protected $description = 'Add new default categories and activities, refresh their keywords, retire the ones that were split up';

    public function handle(FinanceService $finance, ActivityService $activities): int
    {
        $users = User::query()
            ->when($this->option('user'), fn ($query) => $query->whereKey($this->option('user')))
            ->get();

        if ($users->isEmpty()) {
            $this->warn('No users to sync.');

            return self::SUCCESS;
        }

        foreach ($users as $user) {
            $money = $finance->syncDefaults($user);
            $time = $activities->syncDefaults($user);

            $this->line(sprintf(
                '<info>%s</info>%s  money: %d added, %d refreshed, %d switched off%s  time: %d added, %d refreshed',
                $user->email,
                PHP_EOL,
                $money['created'],
                $money['updated'],
                $money['retired'],
                PHP_EOL,
                $time['created'],
                $time['updated'],
            ));
        }

        return self::SUCCESS;
    }
}
