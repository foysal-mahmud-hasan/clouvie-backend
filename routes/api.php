<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaitlistController;
use App\Http\Controllers\VelondraStudioContactController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
| EXPLANATION FOR BEGINNERS:
| - These routes are accessed via: http://your-domain/api/endpoint
| - They're perfect for mobile apps or frontend frameworks (React, Vue, etc.)
| - Routes return JSON data instead of HTML views
|
*/

/*
 * POST /api/register
 * 
 * This endpoint allows users to register by sending:
 * - name (string, required)
 * - email (string, required, must be unique)
 * - password (string, required, min 8 characters)
 * 
 * Returns: JSON with user data and success message
 */
Route::post('/register', [UserController::class, 'register']);

/*
 * POST /api/waitlist
 *
 * This endpoint collects waitlist signups from the marketing site.
 *
 * Expected JSON body:
 * - name (string, required)
 * - email (string, required, unique in waitlist)
 * - monthly_revenue_range (string, optional)
 */
Route::post('/waitlist', [WaitlistController::class, 'store']);

/*
 * POST /api/velondra-studio/contact
 *
 * Contact form for studio.velondra.com.
 *
 * Expected JSON body:
 * - name (string, required)
 * - email (string, required, valid email)
 * - company (string, optional)
 * - budget (string, optional)
 * - services (array of strings, optional)
 * - message (string, required, max 10000 chars)
 *
 * On success: stores in velondra_studio_submissions, emails asif@clouvie.com,
 * sends auto-reply to the submitter, returns 201 JSON.
 */
Route::post('/velondra-studio/contact', [VelondraStudioContactController::class, 'store']);

/*
 * GET /api/users
 * 
 * This endpoint returns all registered users
 * 
 * Returns: JSON array of all users
 */
Route::get('/users', [UserController::class, 'getUsers']);
