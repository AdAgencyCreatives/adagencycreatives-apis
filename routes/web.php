<?php

use App\Http\Controllers\Admin\AgencyController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CreativeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\IndustryController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StrengthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\V1\ChatController;
use App\Http\Controllers\Api\WebSocketController;
use App\Http\Controllers\PlanController;
use App\Jobs\SendEmailJob;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return User::all();
});

Route::get('/email', function () {
    $user = User::find(5);

    $admin = User::find(1);
    SendEmailJob::dispatch([
        'receiver' => $user, 'data' => $user,
    ], 'account_approved');
});

Route::get('/reset', function () {
    Artisan::call('migrate:fresh --seed');
    Artisan::call('optimize:clear');

    echo 'Cache Cleared';

});

Route::group(['middleware' => ['auth', 'admin', 'admin_or_token']], function () {
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

    Route::get('advisor/create', [UserController::class, 'create'])->name('advisor.create');
    Route::get('agency/create', [UserController::class, 'create'])->name('agency.create');
    Route::get('creative/create', [UserController::class, 'create'])->name('creative.create');

    Route::resource('users', UserController::class);
    Route::get('users/{user}/details', [UserController::class, 'details']);
    Route::put('/user/password', [UserController::class, 'updatePassword'])->name('user.password.update');

    Route::put('/agency/{user}', [AgencyController::class, 'update'])->name('agency.update');
    Route::put('/creative/{user}', [CreativeController::class, 'update'])->name('creative.update');
    Route::put('/creative-qualification/{user}', [CreativeController::class, 'update_qualification'])->name('creative.qualification.update');
    Route::put('/creative-educaiton/{user}', [CreativeController::class, 'update_education'])->name('creative.education.update');
    Route::put('/creative-experience/{user}', [CreativeController::class, 'update_experience'])->name('creative.experience.update');

    Route::resource('jobs', JobController::class);
    Route::get('jobs/{job}/details', [JobController::class, 'details']);

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

    include_once 'community.php';

    // log viewer
    Route::get('logs', [LogViewerController::class, 'index'])->middleware('admin');
});

Route::resource('plans', PlanController::class);
Route::view('/pricing', 'pricing');

Route::view('/subscription', 'subscription');
Route::post('subscription', [PlanController::class, 'subscription'])->name('subscription.create');

Route::get('test-web', [WebSocketController::class, 'index']);

Route::group(['middleware' => ['auth']], function () {

    Route::view('/chat', 'chat');
    Route::view('/chat2', 'chat2');
});

Route::get('all-messages', [ChatController::class, 'fetchMessages']);

Route::get('impersonate/{user}', [UserController::class, 'impersonate'])->name('impersonate');

Route::get('/redirect-to-react/{token}', function ($token) {
    return view('pages.users.impersonate', compact('token'));
})->name('react.app');
