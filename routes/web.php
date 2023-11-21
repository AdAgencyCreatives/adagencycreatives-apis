<?php

use App\Http\Controllers\Admin\AgencyController;
use App\Http\Controllers\Admin\AttachmentController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CreativeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\IndustryController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PackageRequestController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StrengthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Admin\CreativeSpotlightController;
use App\Http\Controllers\Api\V1\ResumeController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use App\Http\Controllers\Api\WebSocketController;
use App\Http\Controllers\PlanController;
use App\Jobs\SendEmailJob;
use App\Models\Category;
use App\Models\Group;
use App\Models\Industry;
use App\Models\Location;
use App\Models\Media;
use App\Models\Strength;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

// Route::get('/', function () {
//     return view('welcome');
// });

Route::redirect('/', '/login');

Route::get('/users2', function () {
    if(env('APP_ENV') == 'local'){
        return User::all();
    }
});


Route::get('/email', function () {

    $data = [
        'agency_name' => 'Recipient',
        'job_title' => 'Custom MEssage',
    ];
    return view('emails.application.interested', compact('data'));
    $recipient = User::find(2);
    $sender = User::find(4);

    $data = [
        'recipient' => $recipient->first_name,
        'message' => 'Custom MEssage',
        'message_sender_name' => $sender->first_name,
        'message_sender_profile_url' => get_profile_picture($sender),
        'message_count' => 6,
        'profile_url' => env('FRONTEND_URL') . '/profile/',
    ];

    SendEmailJob::dispatch([
        'receiver' => $recipient,
        'data' => $data,
    ], 'unread_message');
});

Route::get('/reset-messages', function () {
    DB::table('messages')->truncate();
    echo 'Messages Cleared';

});

// Download Resume
Route::get('download/resume', [ResumeController::class, 'download_resume'])->name('download.resume');

// Approve | Deny User from email
Route::get('/user/approve/{uuid}', [UserController::class, 'activate'])->name('user.activate');
Route::get('/user/deny/{uuid}', [UserController::class, 'deactivate'])->name('user.deactivate');

Route::get('advisor/impersonate/{uuid}', [UserController::class, 'advisor_impersonate'])->name('advisor.impersonate');

Route::group(['middleware' => ['auth']], function () {


    Route::group(['middleware' => ['admin']], function () {
        // Taxonomies
        Route::get('state/create', [LocationController::class, 'create'])->name('state.create');
        Route::get('city/create', [LocationController::class, 'city_create'])->name('city.create');
        Route::resource('locations', LocationController::class);
        Route::get('locations/{location}/cities', [LocationController::class, 'cities']);
        Route::resource('categories', CategoryController::class);
        Route::resource('industries', IndustryController::class);
        Route::resource('medias', MediaController::class);
        Route::resource('experiences', ExperienceController::class);
        Route::resource('strengths', StrengthController::class);

        Route::resource('reports', ReportController::class);

        Route::get('/redirect-to-react/{token}', function ($token) {
            return view('pages.users.impersonate', compact('token'));
        })->name('react.app');

        // log viewer
        Route::get('logs', [LogViewerController::class, 'index']);

        include_once 'community.php';

        Route::resource('users', UserController::class);
        Route::get('advisor/create', [UserController::class, 'create'])->name('advisor.create');
        Route::get('agency/create', [UserController::class, 'create'])->name('agency.create');
        Route::get('creative/create', [UserController::class, 'create'])->name('creative.create');

        Route::put('/user/password', [UserController::class, 'updatePassword'])->name('user.password.update');
        Route::put('/user/profile_picture/{user}', [UserController::class, 'update_profile_picture'])->name('user.profile.picture');

        Route::put('/agency/{user}', [AgencyController::class, 'update'])->name('agency.update');
        Route::put('/agency/seo/{user}', [AgencyController::class, 'update_seo'])->name('agency.seo.update');

        /**
         * Package Update
         */
        Route::put('/agency/package/{user}', [SubscriptionController::class, 'update_package'])->name('agency.package.update');

        Route::put('/creative/{user}', [CreativeController::class, 'update'])->name('creative.update');
        Route::put('/creative-qualification/{user}', [CreativeController::class, 'update_qualification'])->name('creative.qualification.update');
        Route::put('/creative-educaiton/{user}', [CreativeController::class, 'update_education'])->name('creative.education.update');
        Route::put('/creative-experience/{user}', [CreativeController::class, 'update_experience'])->name('creative.experience.update');
        Route::put('/creative/seo/{user}', [CreativeController::class, 'update_seo'])->name('creative.seo.update');

        /**
         * SEO
         */
        Route::resource('website-seo', SeoController::class);

        Route::get('impersonate/{user}', [UserController::class, 'impersonate'])->name('impersonate');

        /**
         * Settings
         */
        Route::put('settings/job', [SettingsController::class, 'update_job'])->name('settings.job');
        Route::put('settings/creatives', [SettingsController::class, 'update_creatives'])->name('settings.creatives');
        Route::put('settings/creatives-spotlight', [SettingsController::class, 'update_creative_spotlight'])->name('settings.creative_spotlight');
        Route::resource('settings', SettingsController::class);

        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        Route::resource('creatives', UserController::class)->parameters([
            'creatives' => 'user',
        ]);
        Route::resource('agencies', UserController::class)->parameters([
            'agencies' => 'user',
        ]);
        Route::resource('advisors', UserController::class)->parameters([
            'advisors' => 'user',
        ]);

        Route::resource('users', UserController::class)->only('index', 'details');
        Route::get('users/{user}/details', [UserController::class, 'details']);

        /**
         * (Job) Package Requests
         */
        Route::resource('job-requests', PackageRequestController::class);
        Route::get('job-requests/{request}/details', [PackageRequestController::class, 'details']);

        /**
         * Jobs
         */
        Route::resource('jobs', JobController::class);
        Route::get('jobs/{job}/details', [JobController::class, 'details']);
        Route::put('/jobs/seo/{job}', [JobController::class, 'update_seo'])->name('jobs.seo.update');

        /**
         * Attachment Media
         */
        Route::resource('attachments', AttachmentController::class);
        Route::resource('creative_spotlights', CreativeSpotlightController::class);

        /**
         * Pages Management
         */
        Route::post('image/store', [PageController::class, 'store_img'])->name('image.store');
        Route::resource('pages', PageController::class);

        /**
         * Import Attachments from WP
         */
        Route::get('import/agency-logos', function () {
            $startIndex = isset($_GET['start']) ? intval($_GET['start']) : 0;
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;
            Artisan::call('import:agency-logos', ['startIndex' => $startIndex, 'limit' => $limit]);
        });

        Route::get('import/creatives-profiles', function () {
            $startIndex = isset($_GET['start']) ? intval($_GET['start']) : 0;
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;
            Artisan::call('import:creatives-profiles', ['startIndex' => $startIndex, 'limit' => $limit]);
        });

        Route::get('import/creative-spotlights', function () {
            $startIndex = isset($_GET['start']) ? intval($_GET['start']) : 0;
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;
            Artisan::call('import:creative-spotlights', ['startIndex' => $startIndex, 'limit' => $limit]);
        });

        Route::get('import/creative-portfolio', function () {
            $startIndex = isset($_GET['start']) ? intval($_GET['start']) : 0;
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;
            Artisan::call('import:creative-portfolio', ['startIndex' => $startIndex, 'limit' => $limit]);
        });

        Route::get('import/creative-websites', function () {
            $startIndex = isset($_GET['start']) ? intval($_GET['start']) : 0;
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;
            Artisan::call('import:creative-portfolio-websites-image', ['startIndex' => $startIndex, 'limit' => $limit]);
        });

        Route::get('migrate-fresh-seed', function () {
            Artisan::call('migrate:fresh --seed');
        });

    });

});

Route::resource('plans', PlanController::class);
Route::view('/pricing', 'pricing');
Route::view('/subscription', 'subscription');
Route::post('subscription', [PlanController::class, 'subscription'])->name('subscription.create');
Route::post('stripe/webhook2', [SubscriptionController::class, 'webhook'])->name('stripe.webhook');

Route::get('test-web', [WebSocketController::class, 'index']);

Route::group(['middleware' => ['auth']], function () {

    Route::view('/chat', 'chat');
    Route::view('/chat2', 'chat2');
});

Route::get('all-messages', [ChatController::class, 'fetchMessages']);

// Extras
Route::get('get_uuids', function () {
    create_notification(1, 'This is test');
    $agency_user = User::where('role', 3)->first()->uuid;
    $creative_user = User::find(10)->uuid;
    $advisor_user = User::find(16)->uuid;
    $category = Category::find(5)->uuid;
    $state = Location::find(30)->uuid;
    $city = Location::find(34)->uuid;
    $industry_experience = Industry::find(5)->uuid;
    $media_experience = Media::find(5)->uuid;
    $character_strengths = Strength::find(5)->uuid;

    $jsonObject = [
        'user_id' => $agency_user,
        'category_id' => $category,

        'state_id' => $state,
        'city_id' => $city,
        'industry_experience' => [
            $industry_experience,
        ],
        'media_experience' => [
            $media_experience,
        ],
        'strength' => [
            $character_strengths,
        ],

    ];

    return response()->json($jsonObject, 200);
});

Route::view('resume', 'resume');