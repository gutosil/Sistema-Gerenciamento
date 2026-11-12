<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\ContractTypeController;
use App\Http\Controllers\Api\CustomerTypeController;
use App\Http\Controllers\Api\InstallmentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('users', UserController::class);
Route::apiResource('clients', ClientController::class);
Route::apiResource('contracts', ContractController::class);
Route::apiResource('installments', InstallmentController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('customer-types', CustomerTypeController::class);
Route::apiResource('contract-types', ContractTypeController::class);
