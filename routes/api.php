<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
/**
 * ?Routes v1
 */
Route::prefix('v1')->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::post('/auth/register', 'register');
        Route::post('/auth/login', 'login');
    });



    Route::group(['middleware' => ["auth:api"]], function () {
        /**
         * ? Auth Routes
         */
        Route::controller(AuthController::class)->group(function () {
            Route::get('auth/profile', 'userProfile');
            Route::post('auth/logout', 'logout');
        });
        /**
         * ? Routes Users
         */
        Route::controller(UserController::class)->group(function () {
            Route::post('auth/changePassword', 'changePassword');
            Route::post('auth/check-password', 'checkThePassword');
            Route::put('auth/editProfile', 'editProfile');
        });
        /**
         * ? Routes Contacts
         */

        Route::controller(ContactController::class)->group(function () {
            Route::get('contact', 'index');
            Route::post('contact', 'store');
            Route::get('contact/{id}', 'show');
            Route::put('contact/{id}', 'update');
            Route::post('contact/restore', 'restoreContacts');
            Route::delete('contact/{id}', 'destroy');
        });

        /**
         * ? Routes Favorites
         */

        Route::apiResource('favorite', FavoriteController::class)
            ->only('index', 'store', 'destroy', 'show');
    });
});
