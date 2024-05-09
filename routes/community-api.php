<?php

use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\FriendshipController;
use App\Http\Controllers\Api\V1\GroupController;
use App\Http\Controllers\Api\V1\GroupInvitationController;
use App\Http\Controllers\Api\V1\GroupMemberController;
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
    Route::resource('group-invitations', GroupInvitationController::class);

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
    Route::post('friendships-admin', [FriendshipController::class, 'sendFriendRequestAdmin']);
    Route::patch('friendships', [FriendshipController::class, 'respondToFriendRequest']);
    Route::post('friendships/terminate', [FriendshipController::class, 'unfriend']);

    /**
     * Group Members
     */
    Route::post('leave/membership', [GroupMemberController::class, 'leave_membership']);
    Route::resource('group-members', GroupMemberController::class);

    /**
     * Lounge sidebar stats
     */
    Route::get('lounge/counts', [GroupController::class, 'sidebar_count']);
    Route::get('lounge/main_feed', [PostController::class, 'main_feed']);
});