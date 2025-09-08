<?php

use App\Http\Controllers\Api\V1\FeaturedLocationController;
use App\Http\Controllers\Api\V1\FeaturedLocationWithJobCountController;
use App\Http\Controllers\Api\V1\FriendshipController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\TestDataController;
use App\Http\Controllers\Api\V1\ActivityController;
use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\AgencyController;
use App\Http\Controllers\Api\V1\ApplicationController;
use App\Http\Controllers\Api\V1\ArticlesController;
use App\Http\Controllers\Api\V1\AttachmentController;
use App\Http\Controllers\Api\V1\BookmarkController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\CreativeController;
use App\Http\Controllers\Api\V1\CreativeSpotlightController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\EducationController;
use App\Http\Controllers\Api\V1\EmploymentTypeController;
use App\Http\Controllers\Api\V1\ErrorNotificationController;
use App\Http\Controllers\Api\V1\ExperienceController;
use App\Http\Controllers\Api\V1\FestivalController;
use App\Http\Controllers\Api\V1\GroupController;
use App\Http\Controllers\Api\V1\IndustryController;
use App\Http\Controllers\Api\V1\JobAlertController;
use App\Http\Controllers\Api\V1\JobController;
use App\Http\Controllers\Api\V1\LikeController;
use App\Http\Controllers\Api\V1\LinkController;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Controllers\Api\V1\MentorResourceController;
use App\Http\Controllers\Api\V1\FaqController;
use App\Http\Controllers\Api\V1\MentorTopicController;
use App\Http\Controllers\Api\V1\NoteController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\PackageRequestController;
use App\Http\Controllers\Api\V1\PhoneController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\PostReactionController;
use App\Http\Controllers\Api\V1\PublicationResourceController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\ResumeController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\ScheduleNotificationController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\StrengthController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\YearsOfExperienceController;
use App\Http\Controllers\Api\V1\UtilsController;
use App\Models\Application;
use App\Models\Friendship;
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
Route::get('home/creatives', [CreativeController::class, 'homepage_creatives']);
Route::get('creatives', [CreativeController::class, 'index']);
Route::get('jobs', [JobController::class, 'index']);

Route::get('home/jobs/search', [JobController::class, 'jobs_homepage']);

/**
 * Agency Search
 */
Route::get('agencies/search1', [AgencyController::class, 'search1']); // For agency directory page
Route::get('agencies/search2', [AgencyController::class, 'search2']); //For agency Detail page

Route::get('links', [LinkController::class, 'index'])->name('links.index');
Route::get('attachments', [AttachmentController::class, 'index']);
Route::get('experiences', [ExperienceController::class, 'index']);
Route::get('educations', [EducationController::class, 'index']);
Route::get('home/creative-spotlights', [CreativeSpotlightController::class, 'homepage_spotlights']);
Route::put('creative-spotlights/{uuid}', [CreativeSpotlightController::class, 'update']);
Route::resource('creative-spotlights', CreativeSpotlightController::class)->only('index');
Route::get('creative-spotlights/{slug}', [CreativeSpotlightController::class, 'getSpotlight']);

//Filters
Route::get('get_categories', [CategoryController::class, 'get_categories']);
Route::get('get_categories/creative_count', [CategoryController::class, 'get_categories_with_creatives_count']);
Route::get('get_industry-experiences', [IndustryController::class, 'get_industries']);
Route::get('get_media-experiences', [MediaController::class, 'get_medias']);
// Route::get('employment_types', [JobController::class, 'get_employment_types']);
Route::get('employment_types', [EmploymentTypeController::class, 'get_employment_types']);
Route::get('get_resume_employment_types', [EmploymentTypeController::class, 'get_resume_employment_types']);
Route::get('locations', [LocationController::class, 'index']);
Route::get('cities', [LocationController::class, 'cities']); //For Fetching Only Cities
Route::get('get_strengths', [StrengthController::class, 'get_strengths']);

Route::apiResource('years-of-experience', YearsOfExperienceController::class);
Route::apiResource('employments', EmploymentTypeController::class)->names([
    'index' => 'api.employments.index',
    'store' => 'api.employments.store',
    'show' => 'api.employments.show',
    'update' => 'api.employments.update',
    'destroy' => 'api.employments.destroy',
    'get_employment_types' => 'api.employments.get_employment_types',
    'get_resume_employment_types' => 'api.employments.get_resume_employment_types',
]);
//auth:sanctum
Route::middleware(['auth:sanctum'])->group(function () {

    /**
     * Creatives
     */
    Route::get('creatives/search1', [CreativeController::class, 'search1']);
    Route::get('creatives/search2', [CreativeController::class, 'search2']);
    Route::get('creatives/search3', [CreativeController::class, 'search3']);
    Route::get('creatives/search4', [CreativeController::class, 'search4']);
    Route::get('creatives/search5', [CreativeController::class, 'search_test']);
    Route::get('creatives/search6', [CreativeController::class, 'search6']);

    Route::get('creatives/related', [CreativeController::class, 'related_creatives']);
    Route::get('creatives/search/tag', [CreativeController::class, 'get_tag_creatives']);
    Route::get('resume/system-generated', [CreativeController::class, 'get_system_resume_url']);
    Route::get('creatives/capture-portfolio-snapshot/{uuid}', [UserController::class, 'capturePortfolioSnapshot']);
    Route::get('creatives/remove-portfolio-capture-log/{uuid}', [UserController::class, 'removePortfolioCaptureLog']);

    /**
     * Recruiters
     */
    Route::get('recruiters/search1', [AgencyController::class, 'search1']); //For recruiters directory page

    /**
     * Job Board Routes
     */
    Route::patch('agency_profile/{user}', [AgencyController::class, 'update_profile']);
    Route::patch('advisor_profile/{user}', [AgencyController::class, 'update_profile_advisor']);
    Route::patch('creative_profile/{user}', [CreativeController::class, 'update_profile']);
    Route::patch('creative_resume/{user}', [CreativeController::class, 'update_resume']);
    // Custom route to get the latest VIP agency & creative
    Route::get('agencies/vip/latest', [AgencyController::class, 'getLatestVipAgency'])->name('api.agencies.vip.latest');
    Route::get('creatives/vip/latest', [CreativeController::class, 'getLatestVipCreative'])->name('api.creatives.vip.latest');

    Route::apiResource('agencies', AgencyController::class, ['except' => ['index']])->middleware('check.permissions:agency')->names([
        'index' => 'api.agencies.index',
        'store' => 'api.agencies.store',
        'show' => 'api.agencies.show',
        'update' => 'api.agencies.update',
        'destroy' => 'api.agencies.destroy',
    ]);
    Route::apiResource('creatives', CreativeController::class, ['except' => ['index']])->middleware('check.permissions:creative')->names([
        'index' => 'api.creatives.index',
        'store' => 'api.creatives.store',
        'show' => 'api.creatives.show',
        'update' => 'api.creatives.update',
        'destroy' => 'api.creatives.destroy',
    ]);

    Route::get('home/jobs/search/logged_in', [JobController::class, 'jobs_homepage_logged_in']);
    Route::get('jobs/logged_in', [JobController::class, 'jobs_for_logged_in']);
    Route::apiResource('jobs', JobController::class, ['except' => ['index']])->middleware('check.permissions:job')->names([
        'index' => 'api.jobs.index',
        'store' => 'api.jobs.store',
        'show' => 'api.jobs.show',
        'update' => 'api.jobs.update',
        'destroy' => 'api.jobs.destroy',
    ]);

    Route::apiResource('links', LinkController::class, ['except' => ['index']]);
    Route::post('delete_job_logo', [AttachmentController::class, 'delete_job_logo']);
    Route::apiResource('attachments', AttachmentController::class, ['except' => ['index']])->names([
        'index' => 'api.attachments.index',
        'store' => 'api.attachments.store',
        'show' => 'api.attachments.show',
        'update' => 'api.attachments.update',
        'destroy' => 'api.attachments.destroy',
    ]);
    Route::get('generate-thumbnail-attachment', [AttachmentController::class, 'generateThumbnailAttachment']);
    Route::get('generate-cropped-attachment', [AttachmentController::class, 'generateCroppedAttachment']);

    Route::get('applied_jobs', [ApplicationController::class, 'applied_jobs']);
    Route::get('get_all_applications', [ApplicationController::class, 'get_all_applications']);
    Route::apiResource('applications', ApplicationController::class); //->middleware('check.permissions:application');
    Route::apiResource('resumes', ResumeController::class)->middleware('check.permissions:resume');
    Route::apiResource('educations', EducationController::class, ['except' => ['index', 'update']]); //->middleware('check.permissions:education');
    Route::apiResource('experiences', ExperienceController::class, ['except' => ['index']])->names([
        'index' => 'api.experiences.index',
        'store' => 'api.experiences.store',
        'show' => 'api.experiences.show',
        'update' => 'api.experiences.update',
        'destroy' => 'api.experiences.destroy',
    ]); //->middleware('check.permissions:experience');

    Route::patch('educations', [EducationController::class, 'update']); //->middleware('check.permissions:education');
    Route::patch('experiences', [ExperienceController::class, 'update']); //->middleware('check.permissions:education');

    Route::apiResource('phone-numbers', PhoneController::class);
    Route::apiResource('addresses', AddressController::class);

    Route::apiResource('notes', NoteController::class);
    Route::apiResource('bookmarks', BookmarkController::class);
    Route::apiResource('categories', CategoryController::class)->names([
        'index' => 'api.categories.index',
        'store' => 'api.categories.store',
        'show' => 'api.categories.show',
        'update' => 'api.categories.update',
        'destroy' => 'api.categories.destroy',
        'get_categories' => 'api.categories.get_categories',
        'get_categories_with_creatives_count' => 'api.categories.get_categories_with_creatives_count',
    ]);
    Route::apiResource('strengths', StrengthController::class)->names([
        'index' => 'api.strengths.index',
        'store' => 'api.strengths.store',
        'show' => 'api.strengths.show',
        'update' => 'api.strengths.update',
        'destroy' => 'api.strengths.destroy',
    ]);

    Route::apiResource('industry-experiences', IndustryController::class);
    Route::apiResource('media-experiences', MediaController::class);

    Route::apiResource('reviews', ReviewController::class);
    Route::apiResource('users', UserController::class)->except(['store'])->names([
        'index' => 'api.users.index',
        'store' => 'api.users.store',
        'show' => 'api.users.show',
        'update' => 'api.users.update',
        'destroy' => 'api.users.destroy',
    ]);

    Route::put('jobs/{uuid}/admin', [JobController::class, 'updateFromAdmin']);

    /**
     * Filters
     */
    Route::apiResource('locations', LocationController::class, ['except' => ['index']])->middleware('check.permissions:job')->names([
        'index' => 'api.locations.index',
        'store' => 'api.locations.store',
        'show' => 'api.locations.show',
        'update' => 'api.locations.update',
        'destroy' => 'api.locations.destroy',
        'cities' => 'api.locations.cities',
        'get_states' => 'api.locations.get_states',
        'get_cities' => 'api.locations.get_cities',
    ]);
    Route::get('get_users/posts', [UserController::class, 'get_users_for_posts']);
    Route::get('get_users/attachments', [UserController::class, 'get_users_for_attachments']); //for getting users with attachment counts
    Route::get('get_users/groups', [UserController::class, 'get_users_for_groups']);
    Route::get('get_users/spotlights', [UserController::class, 'get_creatives']);
    Route::get('get_users/festivals', [FestivalController::class, 'get_festival_creatives']); //for getting creatives on festival page
    Route::get('get_users', [UserController::class, 'get_all_users']); //for getting creatives on festival page

    /**
     * Job Alerts
     */
    Route::apiResource('job-alerts', JobAlertController::class);
    Route::post('add-remove-job-alerts', [JobAlertController::class, 'addRemoveJobAlerts']);
    Route::apiResource('package-requests', PackageRequestController::class);
    Route::get('get_assigned_agencies', [PackageRequestController::class, 'get_assigned_agencies']); //Get assigned agencies for advisor

    /**
     * Community Routes
     */
    Route::post('groups/update/{group}', [GroupController::class, 'update']);
    Route::apiResource('groups', GroupController::class)->names([
        'index' => 'api.groups.index',
        'store' => 'api.groups.store',
        'show' => 'api.groups.show',
        'update' => 'api.groups.update',
        'destroy' => 'api.groups.destroy',
        'create' => 'api.groups.create',
    ]);
    Route::get('get_groups', [GroupController::class, 'get_groups']);
    Route::get('trending_posts', [PostController::class, 'trending_posts']);
    Route::apiResource('posts', PostController::class);
    Route::apiResource('comments', CommentController::class);
    Route::apiResource('likes', LikeController::class);
    Route::apiResource('post-reactions', PostReactionController::class)->only(['index', 'store']);
    Route::apiResource('schedule-notifications', ScheduleNotificationController::class);
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
    //Route::patch('messages/{senderId}', [ChatController::class, 'mark_as_read']);
    Route::get('messages/count', [ChatController::class, 'count']);
    Route::get('messages/{receiverId}', [ChatController::class, 'index']);
    Route::get('my-contacts', [ChatController::class, 'getAllMessageContacts']);
    Route::apiResource('messages', ChatController::class);
    Route::post('notifications/send-lounge-mention-notifications', [NotificationController::class, 'sendLoungeMentionNotifications']);
    Route::get('notifications/count', [NotificationController::class, 'count']);
    Route::apiResource('notifications', NotificationController::class);
    Route::get('activities/count', [ActivityController::class, 'count']);
    Route::apiResource('activities', ActivityController::class);
    Route::post('delete-conversation', [ChatController::class, 'deleteConversation']);
    Route::post('delete-single-message/{id}', [ChatController::class, 'deleteSingleMessage']);
    /**
     * SEO
     */
    Route::resource('website-seo', SeoController::class)->names([
        'index' => 'api.website-seo.index',
        'store' => 'api.website-seo.store',
        'show' => 'api.website-seo.show',
        'update' => 'api.website-seo.update',
        'destroy' => 'api.website-seo.destroy',
        'edit' => 'api.website-seo.edit',
    ]);

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
    Route::patch('confirm_password', [UserController::class, 'confirm_password']);
});

Route::get('stats', [DashboardController::class, 'index']);

Route::apiResource('festivals', FestivalController::class)->names([
    'index' => 'api.festivals.index',
    'store' => 'api.festivals.store',
    'show' => 'api.festivals.show',
    'update' => 'api.festivals.update',
    'destroy' => 'api.festivals.destroy',
]);
Route::post('contact-us', [UserController::class, 'contact_us_form_info']);
Route::get('pages', [PageController::class, 'index']);

// Mentorship Topic
Route::resource('topics', MentorTopicController::class);
Route::resource('mentor-resources', MentorResourceController::class);
Route::resource('publication-resources', PublicationResourceController::class);
Route::resource('featured_cities', FeaturedLocationController::class);
// Route::resource('featured_cities', FeaturedLocationWithJobCountController::class);
Route::resource('featured_cities_with_job_count', FeaturedLocationWithJobCountController::class);

// Applications
Route::post('applications/remove_from_recent/{uuid}', [ApplicationController::class, 'remove_from_recent']);
Route::get('get_creative_applications', [ApplicationController::class, 'get_creative_applications']);
Route::get('is_creative_applicant', [ApplicationController::class, 'is_creative_applicant']);
Route::patch('user_update_email_notifications/{user}', [UserController::class, 'update_email_notifications']);

Route::get('/get_base64', [UtilsController::class, 'get_base64']);
Route::post('/notify-error', [ErrorNotificationController::class, 'index']);
Route::get('/get_states', [LocationController::class, 'get_states']);
Route::get('/get_cities', [LocationController::class, 'get_cities']);
Route::get('/get_search_items', [SearchController::class, 'get_search_items']);

Route::get('/get-user-preferred-picture', [CreativeController::class, 'get_user_preferred_picture'])->name('get-user-preferred-picture');

Route::apiResource('faqs', FaqController::class)->names([
    'index' => 'api.faqs.index',
    'store' => 'api.faqs.store',
    'show' => 'api.faqs.show',
    'update' => 'api.faqs.update',
    'destroy' => 'api.faqs.destroy',
]);
Route::get('get_faqs', [FaqController::class, 'get_faqs'])->name('api.faqs.get_faqs');

// news and articles
Route::get('articles/latest', [ArticlesController::class, 'getLatestPosts'])->name('api.articles.latest');

Route::apiResource('articles', ArticlesController::class)->names([
    'index' => 'api.articles.index',
    'store' => 'api.articles.store',
    'show' => 'api.articles.show',
    'update' => 'api.articles.update',
    'destroy' => 'api.articles.destroy',
]);
Route::get('get_articles', [ArticlesController::class, 'get_articles'])->name('api.articles.get_ariticles');
