<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::apiResource('products', ProductController::class);

    Route::post('products/{product}/movements', [StockMovementController::class, 'store']);

    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('suppliers', SupplierController::class);

    Route::get('movements', [StockMovementController::class, 'index']);

    Route::get('reports/inventory-summary', [ReportController::class, 'inventorySummary']);
    Route::get('reports/stock-movements', [ReportController::class, 'stockMovementHistory']);
    Route::get('reports/top-products', [ReportController::class, 'topProducts']);

    Route::get('export/csv', [ExportController::class, 'csv']);
});

Route::middleware(['auth', RoleMiddleware::class.':admin,manager'])->group(function () {
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{product}', [ProductController::class, 'update']);
    Route::delete('products/{product}', [ProductController::class, 'destroy']);
    Route::apiResource('categories', CategoryController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('suppliers', SupplierController::class)->only(['store', 'update', 'destroy']);
});
