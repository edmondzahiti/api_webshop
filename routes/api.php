<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'orders'], function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('{order}', [OrderController::class, 'show']);
    Route::put('{order}', [OrderController::class, 'update']);
    Route::delete('{order}', [OrderController::class, 'destroy']);
    Route::delete('{order}/force-delete', [OrderController::class, 'forceDestroy']);
    Route::post('{order}/add-products', [OrderController::class, 'addProducts']);
    Route::post('{order}/pay', [OrderController::class, 'payOrder']);
});
