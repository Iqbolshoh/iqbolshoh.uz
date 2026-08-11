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
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
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
