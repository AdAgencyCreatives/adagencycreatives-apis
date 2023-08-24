<?php

use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\AgencyController;
use App\Http\Controllers\Api\V1\ApplicationController;
use App\Http\Controllers\Api\V1\AttachmentController;
use App\Http\Controllers\Api\V1\BookmarkController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CreativeController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\EducationController;
use App\Http\Controllers\Api\V1\ExperienceController;
use App\Http\Controllers\Api\V1\IndustryController;
use App\Http\Controllers\Api\V1\JobController;
use App\Http\Controllers\Api\V1\LinkController;
use App\Http\Controllers\Api\V1\NoteController;
use App\Http\Controllers\Api\V1\PhoneController;
use App\Http\Controllers\Api\V1\ResumeController;
use App\Http\Controllers\Api\V1\SubscriptionController;
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

Route::post('/login', [UserController::class, 'login']);
Route::post('/users', [UserController::class, 'store']);
//auth:sanctum
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('creatives', CreativeController::class)->middleware('check.permissions:creative');
    Route::apiResource('agencies', AgencyController::class)->middleware('check.permissions:agency');
    Route::apiResource('jobs', JobController::class)->middleware('check.permissions:job');
    Route::apiResource('applications', ApplicationController::class)->middleware('check.permissions:application');
    Route::apiResource('resumes', ResumeController::class)->middleware('check.permissions:resume');
    Route::apiResource('educations', EducationController::class)->middleware('check.permissions:education');
    Route::apiResource('experiences', ExperienceController::class)->middleware('check.permissions:experience');

    Route::apiResource('phone-numbers', PhoneController::class);
    Route::apiResource('addresses', AddressController::class);
    Route::apiResource('links', LinkController::class);
    Route::apiResource('notes', NoteController::class);
    Route::apiResource('attachments', AttachmentController::class);
    Route::apiResource('bookmarks', BookmarkController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('industries', IndustryController::class);

    Route::apiResource('users', UserController::class)->except(['store']);

    Route::get('subscriptions', [SubscriptionController::class, 'index']);
    Route::get('plans/{plan}', [SubscriptionController::class, 'show']);
    Route::post('subscriptions', [SubscriptionController::class, 'subscription']);
    Route::post('subscriptions/cancel', [SubscriptionController::class, 'cancel']);

    Route::post('logout', [UserController::class, 'logout']);
});
Route::get('stats', [DashboardController::class, 'index']);
