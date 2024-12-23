<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiUserController;

Route::apiResource('user', ApiUserController::class)->middleware('auth:sanctum');
