<?php

use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\PostController;
use Illuminate\Support\Facades\Route;

Route::resource('groups', GroupController::class);
Route::get('groups/{group}/details', [GroupController::class, 'details']);
Route::post('groups/new-member', [GroupController::class, 'add_new_member'])->name('groups.new-member');

Route::resource('posts', PostController::class);
