<?php

use App\Http\Controllers\Admin\BeyondController;
use App\Http\Controllers\Admin\HighlightController;
use App\Http\Controllers\Admin\JourneyController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\ProcessStepController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StatController;
use App\Http\Controllers\Admin\TechStackController;
use App\Http\Controllers\Admin\Finance\CategoryController as FinanceCategoryController;
use App\Http\Controllers\Admin\Finance\OverviewController as FinanceOverviewController;
use App\Http\Controllers\Admin\Finance\SettingController as FinanceSettingController;
use App\Http\Controllers\Admin\Finance\TransactionController;
use App\Http\Controllers\Admin\Plan\AnalyticsController;
use App\Http\Controllers\Admin\Plan\CalendarController;
use App\Http\Controllers\Admin\Plan\ForecastController;
use App\Http\Controllers\Admin\Plan\GoalController;
use App\Http\Controllers\Admin\Plan\NotificationController;
use App\Http\Controllers\Admin\Plan\PlanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Telegram\WebhookController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
| The site itself is a React build served straight by nginx; Laravel only
| owns `/api` and this `/admin` panel, which is why every route here carries
| the prefix.
*/

Route::prefix('admin')->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard.index'))->name('home');

    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // Profile
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile.show');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        // Platform: roles & users
        Route::resource('roles', RoleController::class)->except(['show']);
        Route::resource('users', UserController::class)->except(['show']);

        // Site content
        Route::name('admin.')->group(function () {
            $sections = [
                'projects'      => ProjectController::class,
                'services'      => ServiceController::class,
                'tech-stacks'   => TechStackController::class,
                'stats'         => StatController::class,
                'highlights'    => HighlightController::class,
                'journeys'      => JourneyController::class,
                'beyonds'       => BeyondController::class,
                'process-steps' => ProcessStepController::class,
            ];

            foreach ($sections as $key => $controller) {
                Route::resource($key, $controller)
                    ->except(['show'])
                    ->parameters([$key => 'id'])
                    ->where(['id' => '[0-9]+']);
            }

            // Settings
            Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
            Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

            // ── Plan: goals, daily plans and everything measured from them ──
            Route::resource('goals', GoalController::class)->except(['show']);

            Route::resource('plans', PlanController::class);
            Route::post('plans/{plan}/{action}', [PlanController::class, 'act'])
                ->whereIn('action', ['complete', 'fail', 'postpone'])
                ->name('plans.act');

            Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');

            Route::prefix('analytics')->name('analytics.')->group(function () {
                Route::get('/', [AnalyticsController::class, 'monthly'])->name('index');
                Route::get('/daily', [AnalyticsController::class, 'daily'])->name('daily');
                Route::get('/weekly', [AnalyticsController::class, 'weekly'])->name('weekly');
                Route::get('/monthly', [AnalyticsController::class, 'monthly'])->name('monthly');
            });

            Route::get('/forecast', [ForecastController::class, 'index'])->name('forecast.index');
            Route::post('/forecast', [ForecastController::class, 'store'])->name('forecast.store');

            Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
            Route::post('/notifications/{notification}/retry', [NotificationController::class, 'retry'])->name('notifications.retry');
            Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

            // ── Finance: what came in, what went out, and the limits ──
            Route::get('/finance', [FinanceOverviewController::class, 'index'])->name('finance.index');

            // Declared before the resource so `/transactions/export` can never
            // be read as a transaction id.
            Route::get('/transactions/export', [TransactionController::class, 'export'])->name('transactions.export');
            Route::resource('transactions', TransactionController::class)->except(['show']);

            Route::post('/finance-categories/restore-defaults', [FinanceCategoryController::class, 'restoreDefaults'])
                ->name('finance-categories.restore');
            Route::resource('finance-categories', FinanceCategoryController::class)->except(['show']);

            Route::get('/finance-settings', [FinanceSettingController::class, 'index'])->name('finance-settings.index');
            Route::put('/finance-settings', [FinanceSettingController::class, 'update'])->name('finance-settings.update');

            // Inbox: contact messages and service orders
            Route::prefix('messages/{type}')->name('messages.')->group(function () {
                Route::get('/', [MessageController::class, 'index'])->name('index');
                Route::post('/read-all', [MessageController::class, 'markAllRead'])->name('read-all');
                Route::get('/{id}', [MessageController::class, 'show'])->name('show')->where('id', '[0-9]+');
                Route::delete('/{id}', [MessageController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
            });
        });
    });
});

/*
|--------------------------------------------------------------------------
| Telegram webhook
|--------------------------------------------------------------------------
| Outside the admin group and outside any auth: Telegram authenticates itself
| with a secret header, which the controller checks before anything else.
*/

Route::post('/telegram/webhook', WebhookController::class)->name('telegram.webhook');
