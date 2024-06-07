<?php

use App\Http\Controllers\AuthController;
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

Route::Apiresource('contacts', ContactController::class);

Route::group(['middleware' => ["auth:sanctum"]], function () {
    //auth metodos
    Route::controller(AuthController::class)->group(function () {
        Route::get('auth/profile', 'userProfile');
        Route::post('auth/logout', 'logout');
    });
});

//auth metodos
