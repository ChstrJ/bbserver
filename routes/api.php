<?php

use App\Http\Controllers\Api\V1\AppointmentController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\CategoryController;
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
        Route::get('/employee/summary', [EmployeeController::class, 'getAllSummary']);
        Route::get('/employee/chart/sales', [EmployeeController::class, 'chartSales']);
        Route::get('/employee/chart/products', [EmployeeController::class, 'criticalStocks']);
        Route::apiResource('/orders', TransactionController::class)->except(['update']);
        Route::apiResource('/products', ProductController::class);
        Route::apiResource('/customers', CustomerController::class);
        Route::apiResource('/appointments', AppointmentController::class);
    });

    Route::group(['middleware' => 'admin'], function () {
        //admin scope
        Route::apiResource('/users', UserController::class);
        Route::apiResource('/categories', CategoryController::class)->only(['store', 'update', 'destroy']);
        Route::get('/admin/summary', [AdminController::class, 'getAllSummary']);
        Route::get('/admin/chart/sales', [AdminController::class, 'chartSales']);
        Route::get('/admin/chart/products', [AdminController::class, 'criticalStocks']);
        Route::get('/admin/employees', [FilterController::class, 'filterEmployees']);
        Route::get('/admin/sales', [FilterController::class, 'filterSales']);
        Route::get('/admin/orders', [FilterController::class, 'filterOrders']);
        Route::get('/admin/export', [ExportController::class, 'exportSales']);
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

        Route::post('/admin/create', [AdminController::class, 'createAdmin']);
    });
});
