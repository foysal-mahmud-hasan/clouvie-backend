<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaitlistController;
use App\Http\Controllers\VelondraStudioContactController;

/*
 * BEGINNER'S GUIDE TO WEB ROUTES:
 * - Web routes return HTML pages (views) that users see in their browser
 * - Unlike API routes, these are for human visitors, not apps
 */

// Home page - shows welcome message
// Route::get('/', function () {
//     return view('welcome');
// });

// Users page - shows all registered users
// When someone visits http://your-site.com/, they'll see the users list
Route::get('/', [UserController::class, 'showUsers']);

// Admin waitlist page - shows all waitlist signups
// Example: http://your-site.com/admin/waitlist
Route::get('/clouvie/waitlist', [WaitlistController::class, 'index']);

// Admin view for Velondra Studio contact submissions
// https://backend.clouvie.com/velondra-studio/submissions
Route::get('/velondra-studio/submissions', [VelondraStudioContactController::class, 'index']);
