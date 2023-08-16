<?php

use App\Models\Education;
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
    Education::create([
        'uuid' => 'aab766ac-7e99-3b3b-bc6e-2fd5add26594',
        'resume_id' => 1,
        'degree' => 'abc',
        'college' => 'abc',
        'started_at' => '2022-08-11',
        'completed_at' => '2023-08-11',
    ]);
});
