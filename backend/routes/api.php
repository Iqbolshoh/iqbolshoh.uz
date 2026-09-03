<?php

use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ContentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| iqbolshoh.uz — ochiq API
|--------------------------------------------------------------------------
| Barcha yo'llar `/api` prefiksi bilan ishlaydi (bootstrap/app.php da ulangan).
*/

/*
 * O'qish uchun yo'llar. `etag` — javob o'zgarmagan bo'lsa brauzer 304 oladi
 * va 25 KB ni qayta yuklamaydi; o'zgargan zahoti esa yangisini oladi, ya'ni
 * panelda tahrirlangan matn kechikmaydi. `max-age` ataylab qo'yilmagan:
 * kontent serverda 60 soniya keshlanadi, brauzerda ham keshlansa tahrir ikki
 * baravar kech ko'rinardi.
 */
Route::middleware('cache.headers:etag')->group(function () {
    // Butun sayt kontenti bitta so'rovda — frontend shuni ishlatadi
    Route::get('/content', [ContentController::class, 'index']);

    Route::get('/projects', [ContentController::class, 'projects']);
    Route::get('/services', [ContentController::class, 'services']);
});

// Formalar — spam'ga qarshi daqiqada 5 ta so'rov
Route::middleware('throttle:5,1')->group(function () {
    Route::post('/contact', [ContactController::class, 'store']);
    Route::post('/service-order', [ContactController::class, 'storeServiceOrder']);
});
