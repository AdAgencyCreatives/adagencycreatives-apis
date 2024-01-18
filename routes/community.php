<?php

use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\PostController;
use Illuminate\Support\Facades\Route;

Route::resource('groups', GroupController::class);
Route::get('groups/{group}/details', [GroupController::class, 'details']);
Route::post('groups/new-member', [GroupController::class, 'add_new_member'])->name('groups.new-member');
Route::post('groups/update-member', [GroupController::class, 'update_member_role'])->name('groups.update-member');

Route::resource('posts', PostController::class);
Route::get('posts/{post}/details', [PostController::class, 'details']);
Route::patch('posts/{post}', [PostController::class, 'update'])->name('posts.update');