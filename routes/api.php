<?php

use App\Http\Controllers\Api\V1\AppointmentController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\ExportController;
use App\Http\Controllers\Api\V1\RestoreController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\TransactionController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\FilterController;

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


//public routes
Route::post('/auth/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum', 'online']], function () {
    Route::get('/auth/verify', [AuthController::class, 'verifyToken']);
    Route::get('/auth/user', [AuthController::class, 'getUserInfo']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::group(['middleware' => 'employee'], function () {
        //employee scope
        Route::get('/employee/summary', [EmployeeController::class, 'getAllTotal']);
        Route::get('/employee/chart/sales', [EmployeeController::class, 'chartSales']);
        Route::apiResource('/products', ProductController::class);
        Route::apiResource('/orders', TransactionController::class)->except(['update']);
        Route::apiResource('/customers', CustomerController::class);
        Route::apiResource('/appointments', AppointmentController::class);
    });


    Route::group(['middleware' => 'admin'], function () {
        //admin scope
        Route::apiResource('/users', UserController::class);
        Route::get('/admin/summary', [AdminController::class, 'getAllTotal']);
        Route::get('/admin/employees', [FilterController::class, 'filterEmployees']);
        Route::get('/admin/sales', [FilterController::class, 'filterSales']);
        Route::get('/admin/orders', [FilterController::class, 'filterOrders']);
        Route::get('/admin/export', [ExportController::class, 'exportSales']);
        Route::get('/admin/chart/sales', [AdminController::class, 'chartSales']);
        Route::patch('/admin/order/approve/{id}', [AdminController::class, 'approve']);
        Route::patch('/admin/order/reject/{id}', [AdminController::class, 'reject']);

        //for restoring resources
        Route::get('/admin/deleted-customers', [RestoreController::class, 'getAllDeletedCustomers']);
        Route::get('/admin/deleted-orders', [RestoreController::class, 'getAllDeletedTransactions']);
        Route::get('/admin/deleted-employees', [RestoreController::class, 'getAllDeletedEmployees']);
        Route::get('/admin/deleted-products', [RestoreController::class, 'getAllDeletedProducts']);
        Route::patch('/admin/restore/customer/{id}', [RestoreController::class, 'restoreCustomer']);
        Route::patch('/admin/restore/order/{id}', [RestoreController::class, 'restoreTransaction']);
        Route::patch('/admin/restore/employee/{id}', [RestoreController::class, 'restoreEmployee']);
        Route::patch('/admin/restore/product/{id}', [RestoreController::class, 'restoreProduct']);
    });
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
