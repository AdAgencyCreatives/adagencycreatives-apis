<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PlanController;
use App\Models\User;
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

Route::group(['middleware' => ['auth', 'admin', 'admin_or_token']], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::resource('users', UserController::class);
    Route::get('users/{user}/details', [UserController::class, 'details']);
    Route::put('/user/password', [UserController::class, 'updatePassword'])->name('user.password.update');

    Route::resource('jobs', JobController::class);
    Route::get('jobs/{job}/details', [JobController::class, 'details']);

    Route::resource('locations', LocationController::class);
    Route::get('locations/{location}/cities', [LocationController::class, 'cities']);
    Route::resource('reports', ReportController::class);

    include_once 'community.php';
});

Route::resource('plans', PlanController::class);
Route::view('/pricing', 'pricing');
Route::view('/chat', 'chat');
Route::view('/subscription', 'subscription');
Route::post('subscription', [PlanController::class, 'subscription'])->name('subscription.create');
