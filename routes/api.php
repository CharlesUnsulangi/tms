<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MsTmsDriverApiController;

Route::apiResource('drivers', MsTmsDriverApiController::class);
