<?php

use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\TaskController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum', 'verified'])->name('user.')->group(function () {
    Route::put('user', [UserController::class, 'update'])->name('update');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::resource('tasks', TaskController::class)->only(['index','store','update','destroy']);
});


Route::post('register', [AuthController::class, 'register'])->name('user.register');
Route::post('login', [AuthController::class, 'login'])->name('user.login');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('user.login');
Route::get('email/verify/{id}', [AuthController::class, 'verify'])->name('verification.verify')->middleware('signed');
