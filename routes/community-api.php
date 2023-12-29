<?php

use App\Http\Controllers\Api\V1\FriendshipController;
use App\Http\Controllers\Api\V1\GroupController;
use App\Http\Controllers\Api\V1\GroupMemberController;
use App\Http\Controllers\Api\V1\InvitationController;
use App\Http\Controllers\Api\V1\JobInvitationController;
use Illuminate\Support\Facades\Route;

/**
 * *******************************************************
 * Community API Routes
 * *******************************************************
 */

//auth:sanctum
Route::middleware(['auth:sanctum'])->group(function () {
    /**
     * Groups Invitation
     */
    Route::resource('invitations', InvitationController::class);

    /**
     * Job Invitation
     */
    Route::post('job-invitation', [JobInvitationController::class, 'job_invitation']);

    /**
     * Friendship Invitataion
     */
    Route::get('my-friends', [FriendshipController::class, 'all_friends']);
    Route::get('friendships', [FriendshipController::class, 'index']);
    Route::post('friendships', [FriendshipController::class, 'sendFriendRequest']);
    Route::patch('friendships', [FriendshipController::class, 'respondToFriendRequest']);
    Route::post('friendships/terminate', [FriendshipController::class, 'unfriend']);

    /**
     * Group Members
     */
    Route::resource('group-members', GroupMemberController::class);

    /**
     * Lounge sidebar stats
     */
    Route::get('lounge/counts', [GroupController::class, 'sidebar_count']);
    Route::get('lounge/main_feed', [GroupController::class, 'main_feed']);
});
