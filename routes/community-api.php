<?php

use App\Http\Controllers\Api\V1\InvitationController;
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
