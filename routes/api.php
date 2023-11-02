<?php

use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\AgencyController;
use App\Http\Controllers\Api\V1\ApplicationController;
use App\Http\Controllers\Api\V1\AttachmentController;
use App\Http\Controllers\Api\V1\BookmarkController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\CreativeController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\EducationController;
use App\Http\Controllers\Api\V1\ExperienceController;
use App\Http\Controllers\Api\V1\GroupController;
use App\Http\Controllers\Api\V1\IndustryController;
use App\Http\Controllers\Api\V1\JobAlertController;
use App\Http\Controllers\Api\V1\JobController;
use App\Http\Controllers\Api\V1\LikeController;
use App\Http\Controllers\Api\V1\LinkController;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Controllers\Api\V1\NoteController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\PackageRequestController;
use App\Http\Controllers\Api\V1\PhoneController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\ResumeController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\StrengthController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\YearsOfExperienceController;
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

// Route::post('/password/reset', [PasswordResetController::class, 'reset']);

// Public GET routes
Route::get('agencies', [AgencyController::class, 'index']);
Route::get('creatives', [CreativeController::class, 'index']);
Route::get('jobs', [JobController::class, 'index']);

Route::get('links', [LinkController::class, 'index'])->name('links.index');
Route::get('attachments', [AttachmentController::class, 'index']);
Route::get('experiences', [ExperienceController::class, 'index']);
Route::get('educations', [EducationController::class, 'index']);
Route::get('creative_spotlight', [CreativeController::class, 'creative_spotlight']);

//Filters
Route::get('get_categories', [CategoryController::class, 'get_categories']);
Route::get('get_industry-experiences', [IndustryController::class, 'get_industries']);
Route::get('get_media-experiences', [MediaController::class, 'get_medias']);
Route::get('employment_types', [JobController::class, 'get_employment_types']);
Route::get('locations', [LocationController::class, 'index']);
Route::get('get_strengths', [StrengthController::class, 'get_strengths']);

//auth:sanctum
Route::middleware(['auth:sanctum'])->group(function () {
    /**
     * Job Board Routes
     */
    Route::patch('agency_profile/{user}', [AgencyController::class, 'update_profile']);
    Route::patch('creative_profile/{user}', [CreativeController::class, 'update_profile']);
    Route::patch('creative_resume/{user}', [CreativeController::class, 'update_resume']);
    Route::apiResource('agencies', AgencyController::class, ['except' => ['index']])->middleware('check.permissions:agency');
    Route::apiResource('creatives', CreativeController::class, ['except' => ['index']])->middleware('check.permissions:creative');
    Route::apiResource('jobs', JobController::class, ['except' => ['index']])->middleware('check.permissions:job');

    Route::apiResource('links', LinkController::class, ['except' => ['index']]);
    Route::apiResource('attachments', AttachmentController::class, ['except' => ['index']]);

    Route::get('applied_jobs', [ApplicationController::class, 'applied_jobs']);
    Route::apiResource('applications', ApplicationController::class); //->middleware('check.permissions:application');
    Route::apiResource('resumes', ResumeController::class)->middleware('check.permissions:resume');
    Route::apiResource('educations', EducationController::class, ['except' => ['index', 'update']])->middleware('check.permissions:education');
    Route::apiResource('experiences', ExperienceController::class, ['except' => ['index']])->middleware('check.permissions:experience');

    Route::patch('educations', [EducationController::class, 'update'])->middleware('check.permissions:education');
    Route::patch('experiences', [ExperienceController::class, 'update'])->middleware('check.permissions:education');

    Route::apiResource('phone-numbers', PhoneController::class);
    Route::apiResource('addresses', AddressController::class);

    Route::apiResource('notes', NoteController::class);
    Route::apiResource('bookmarks', BookmarkController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('strengths', StrengthController::class);

    Route::apiResource('industry-experiences', IndustryController::class);
    Route::apiResource('media-experiences', MediaController::class);
    Route::apiResource('years-of-experience', YearsOfExperienceController::class);
    Route::apiResource('reviews', ReviewController::class);
    Route::apiResource('users', UserController::class)->except(['store']);

    Route::put('jobs/{uuid}/admin', [JobController::class, 'updateFromAdmin']);

    /**
     * Filters
     */
    Route::apiResource('locations', LocationController::class, ['except' => ['index']])->middleware('check.permissions:job');
    Route::get('get_users/posts', [UserController::class, 'get_users_for_posts']);
    Route::get('get_users/attachments', [UserController::class, 'get_users_for_attachments']); //for getting users with attachment counts

    /**
     * Job Alerts
     */
    Route::apiResource('job-alerts', JobAlertController::class);
    Route::apiResource('package-requests', PackageRequestController::class);
    Route::get('get_assigned_agencies/{uuid}', [PackageRequestController::class, 'get_assigned_agencies']); //Get assigned agencies for advisor

    /**
     * Community Routes
     */
    Route::apiResource('groups', GroupController::class);
    Route::get('get_groups', [GroupController::class, 'get_groups']);
    Route::apiResource('posts', PostController::class);
    Route::apiResource('comments', CommentController::class);
    Route::apiResource('likes', LikeController::class);

    /**
     * Stripe Payment Routes
     */
    Route::get('packages', [SubscriptionController::class, 'packages']);
    Route::get('subscriptions', [SubscriptionController::class, 'index']);
    Route::get('subscription/status', [SubscriptionController::class, 'status']);
    Route::get('plans/{plan}', [SubscriptionController::class, 'show']);
    Route::post('subscriptions', [SubscriptionController::class, 'subscription']);
    Route::post('subscriptions/cancel', [SubscriptionController::class, 'cancel']);

    /**
     * Chat Routes
     */
    Route::get('messages/{receiverId}', [ChatController::class, 'index']);
    Route::get('my-contacts', [ChatController::class, 'getAllMessageContacts']);
    Route::get('messages', [ChatController::class, 'fetchMessages']);
    Route::apiResource('messages', ChatController::class);
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::patch('notifications', [NotificationController::class, 'update']);

    /**
     * SEO
     */
    Route::resource('website-seo', SeoController::class);

    Route::post('logout', [UserController::class, 'logout']);

    Route::middleware(['admin'])->group(function () {
        Route::get('reports', [ReportController::class, 'sales']);
    });

    Route::post('/re_login', [UserController::class, 're_login']);

    /**
     * Dashboard Stats
     */
    Route::get('agency_stats', [DashboardController::class, 'agency_dashboard_stats']);
    Route::get('creative_stats', [DashboardController::class, 'creative_dashboard_stats']);
    Route::patch('update_password', [UserController::class, 'update_password']);

});

Route::get('stats', [DashboardController::class, 'index']);
