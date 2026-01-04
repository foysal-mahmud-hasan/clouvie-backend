<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/**
 * UserController
 * 
 * This controller handles all user-related operations
 * 
 * BEGINNER'S GUIDE:
 * - Controllers are like the "brain" of your application
 * - They receive requests, process them, and return responses
 * - Think of them as the middleman between routes and your database
 */
class UserController extends Controller
{
    /**
     * Register a new user
     * 
     * HOW IT WORKS:
     * 1. Receives data from the frontend (name, email, password)
     * 2. Validates the data (checks if email is valid, password is strong, etc.)
     * 3. Creates a new user in the database
     * 4. Returns success response with user data
     * 
     * @param Request $request - Contains all the data sent from frontend
     * @return \Illuminate\Http\JsonResponse - JSON response back to frontend
     */
    public function register(Request $request)
    {
        // STEP 1: Validate incoming data
        // Think of validation as a security guard checking IDs at the door
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',        // Name is required, must be text, max 255 chars
            'email' => 'required|string|email|max:255|unique:users',  // Email must be valid and unique
            'password' => 'required|string|min:8',      // Password must be at least 8 characters
        ]);

        // If validation fails, send error message back
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()  // Detailed error messages
            ], 422);  // 422 = Unprocessable Entity (validation error)
        }

        try {
            // STEP 2: Create the user in database
            // Hash::make() encrypts the password for security
            // NEVER store plain text passwords!
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),  // Encrypted password
            ]);

            // STEP 3: Return success response
            // We don't send back the password for security
            return response()->json([
                'success' => true,
                'message' => 'User registered successfully!',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                ]
            ], 201);  // 201 = Created successfully

        } catch (\Exception $e) {
            // If something goes wrong, catch the error
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);  // 500 = Internal Server Error
        }
    }

    /**
     * Get all registered users
     * 
     * HOW IT WORKS:
     * 1. Fetches all users from database
     * 2. Returns them as JSON array
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers()
    {
        try {
            // Fetch all users, ordered by newest first
            // select() specifies which columns to return (we exclude password for security)
            $users = User::select('id', 'name', 'email', 'created_at')
                        ->orderBy('created_at', 'desc')
                        ->get();

            return response()->json([
                'success' => true,
                'count' => $users->count(),  // Total number of users
                'data' => $users
            ], 200);  // 200 = OK

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show users page (for web browser)
     * 
     * HOW IT WORKS:
     * 1. Fetches all users from database
     * 2. Passes them to a Blade view (HTML template)
     * 3. The view displays the users in a nice web page
     * 
     * DIFFERENCE FROM getUsers():
     * - getUsers() returns JSON for APIs
     * - showUsers() returns HTML for web browsers
     * 
     * @return \Illuminate\View\View
     */
    public function showUsers()
    {
        // Get all users from database
        $users = User::select('id', 'name', 'email', 'created_at')
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Pass $users data to the 'users' view
        // The view can access this data using {{ $users }}
        return view('users', compact('users'));
    }
}
