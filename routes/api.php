<?php

use App\Http\Controllers\AuthController;
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
Route::controller(AuthController::class)->group(function () {
    Route::post('/auth/register', 'register');
    Route::post('/auth/login', 'login');
});



Route::group(['middleware' => ["auth:sanctum"]], function () {
    //auth metodos
    Route::controller(AuthController::class)->group(function () {
        Route::get('auth/profile', 'userProfile');
        Route::post('auth/logout', 'logout');
    });
    Route::controller(UserController::class)->group(function () {
        Route::post('auth/changePassword', 'changePassword');
        Route::put('auth/editProfile', 'editProfile');
    });

    Route::controller(ContactController::class)->group(function () {
        Route::get('contact/index', 'index');
        Route::post('contact/create', 'store');
        Route::get('contact/show/{id}', 'show');
        Route::put('contact/update/{id}', 'update');
    });
});

//auth metodos
