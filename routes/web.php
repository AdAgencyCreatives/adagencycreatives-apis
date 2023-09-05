<?php

use App\Http\Controllers\Admin\AgencyController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CreativeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PlanController;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

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

Route::get('/reset', function () {
    Artisan::call("migrate:fresh --seed");
    Artisan::call("optimize:clear");

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

    Route::resource('locations', LocationController::class);
    Route::get('locations/{location}/cities', [LocationController::class, 'cities']);

    Route::resource('categories', CategoryController::class);
    Route::resource('reports', ReportController::class);

    include_once 'community.php';
});

Route::resource('plans', PlanController::class);
Route::view('/pricing', 'pricing');
Route::view('/chat', 'chat');
Route::view('/subscription', 'subscription');
Route::post('subscription', [PlanController::class, 'subscription'])->name('subscription.create');