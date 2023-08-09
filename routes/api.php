<?php

use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\AgencyController;
use App\Http\Controllers\Api\V1\ApplicationController;
use App\Http\Controllers\Api\V1\CreativeController;
use App\Http\Controllers\Api\V1\JobController;
use App\Http\Controllers\Api\V1\LinkController;
use App\Http\Controllers\Api\V1\PhoneController;
use App\Http\Controllers\Api\V1\ResumeController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

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

Route::apiResource('users', UserController::class);
Route::apiResource('agencies', AgencyController::class);
Route::apiResource('creatives', CreativeController::class);
Route::apiResource('jobs', JobController::class);
Route::apiResource('applications', ApplicationController::class);
Route::apiResource('links', LinkController::class);
Route::apiResource('phone-numbers', PhoneController::class);
Route::apiResource('addresses', AddressController::class);
Route::apiResource('resumes', ResumeController::class);
