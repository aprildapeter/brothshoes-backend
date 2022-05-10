<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['auth:sanctum', 'verified', 'admin']], function () {
   
    Route::name('dashboard.')->prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');

        Route::middleware(['admin'])->group(function () {
            Route::resource('category', ProductCategoryController::class);
            Route::resource('product', ProductController::class);
            Route::resource('user', UserController::class);
            Route::resource('transaction', TransactionController::class)->only([
                'index', 'show', 'edit', 'update'
            ]);
        });
    });
});
