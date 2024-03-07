<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\CustomerController;

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

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/users/', [UserController::class, 'index']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::apiResource('/products', ProductController::class);
    Route::apiResource('/pos', TransactionController::class);
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/customers', CustomerController::class);
});






// Route::get('/products', [ProductController::class, 'index']);
//     Route::get('/users', [UserController::class, 'index']);
//     Route::post('/users', [UserController::class, 'store']);
//     Route::get('/users/{id}', [UserController::class, 'show']);
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::get('/products/{id}', [ProductController::class, 'show']);
//     Route::post('/products', [ProductController::class, 'store']);
//     Route::patch('/products/{id}', [ProductController::class, 'edit']);
//     Route::put('/products/{id}', [ProductController::class, 'update']);
//     Route::delete('/products/{id}', [ProductController::class, 'destroy']);
//     Route::post('/pos', [TransactionController::class, 'store']);
//     Route::get('/pos/', [TransactionController::class, 'index']);
//     Route::get('/pos/{id}', [TransactionController::class, 'show']);
//     Route::delete('/pos/{id}', [TransactionController::class, 'destroy']);
//     Route::put('/pos/{id}', [TransactionController::class, 'update']);
//     Route::patch('/pos/{id}', [TransactionController::class, 'update']);
