<?php

use App\Http\Controllers\Admin\GroupController;
use Illuminate\Support\Facades\Route;

Route::resource('groups', GroupController::class);
Route::get('groups/{group}/details', [GroupController::class, 'details']);
