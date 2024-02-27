<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\TransactionController;


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

Route::get('/v1/users', [UserController::class, 'index']);
Route::post('/v1/users', [UserController::class, 'store']);
Route::get('/v1/users/{id}', [UserController::class, 'show']);

Route::post('/v1/auth', [AuthController::class, 'login']);
Route::post('/v1/register', [AuthController::class, 'register']);

Route::get('/v1/products', [ProductController::class, 'index']);
Route::get('/v1/products/{id}', [ProductController::class, 'show']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/v1/logout', [AuthController::class, 'logout']);
    Route::post('/v1/products', [ProductController::class, 'store']);
    Route::patch('/v1/products/{id}', [ProductController::class, 'edit']);
    Route::put('/v1/products/{id}', [ProductController::class, 'update']);
    Route::delete('/v1/products/{id}', [ProductController::class, 'destroy']);
    Route::post('/v1/pos', [TransactionController::class, 'store']);
    Route::get('/v1/pos/', [TransactionController::class, 'index']);
    Route::get('/v1/pos/{id}', [TransactionController::class, 'show']);
    Route::delete('/v1/pos/{id}', [TransactionController::class, 'destroy']);
    Route::put('/v1/pos/{id}', [TransactionController::class, 'update']);
    Route::patch('/v1/pos/{id}', [TransactionController::class, 'update']);
});







