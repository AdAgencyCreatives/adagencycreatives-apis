<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\UserController;
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
});
