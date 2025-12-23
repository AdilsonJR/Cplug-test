<?php

use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;

Route::post('/inventory', [InventoryController::class, 'store']);
Route::get('/inventory', [InventoryController::class, 'index']);
Route::post('/sales', [SalesController::class, 'store']);
Route::get('/sales/{id}', [SalesController::class, 'show']);
Route::get('/reports/sales', [ReportsController::class, 'sales']);
