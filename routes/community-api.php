<?php

use App\Http\Controllers\Api\V1\InvitationController;
use App\Http\Controllers\Api\V1\JobController;
use Illuminate\Support\Facades\Route;

/**
 * *******************************************************
 * Community API Routes
 * *******************************************************
 */

/**
 * Groups Invitation
 */
Route::resource('invitations', InvitationController::class);

/**
 * Job Invitation
 */
Route::post('job-invitation', [JobController::class, 'job_invitation']);
