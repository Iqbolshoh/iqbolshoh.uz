<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\FinanceService;
use Illuminate\Console\Command;

/**
 * Bring every account's categories in line with the shipped catalogue.
 *
 * The panel has a button for this, but a deploy that adds a category should
 * not wait for somebody to log in and press it — the bot would go on filing
 * "kafe" under Groceries until they did. So it belongs in the deploy steps,
 * next to `migrate --force`.
 */
class SyncFinanceCategories extends Command
{
    protected $signature = 'finance:sync-categories {--user= : Only this user id}';

    protected $description = 'Add new default categories, refresh their keywords, retire the ones that were split up';

    public function handle(FinanceService $finance): int
    {
        $users = User::query()
            ->when($this->option('user'), fn ($query) => $query->whereKey($this->option('user')))
            ->get();

        if ($users->isEmpty()) {
            $this->warn('No users to sync.');

            return self::SUCCESS;
        }

        foreach ($users as $user) {
            $result = $finance->syncDefaults($user);

            $this->line(sprintf(
                '<info>%s</info> — %d added, %d refreshed, %d switched off',
                $user->email,
                $result['created'],
                $result['updated'],
                $result['retired']
            ));
        }

        return self::SUCCESS;
    }
}
