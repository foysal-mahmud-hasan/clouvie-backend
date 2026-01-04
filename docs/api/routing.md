# 🔌 API Routing in Laravel

> **How to define endpoints that your frontend can call**

---

## 📋 Table of Contents
- [What is Routing?](#what-is-routing)
- [Setting Up API Routes](#setting-up-api-routes)
- [Why Separate API Routes?](#why-separate-api-routes)
- [Route Basics](#route-basics)
- [HTTP Methods](#http-methods)
- [Our Routes Explained](#our-routes-explained)
- [Route Parameters](#route-parameters)
- [Best Practices](#best-practices)

---

## 🤔 What is Routing?

**Routing** is like a GPS for your application. It maps URLs to specific code that should run.

```
User types URL → Route matches it → Controller handles it → Response sent back
```

**Simple Example:**
```php
Route::get('/hello', function() {
    return 'Hello World!';
});
```

When someone visits `http://yoursite.com/hello`, they see "Hello World!".

---

## ⚙️ Setting Up API Routes

### 🚨 Important: Laravel 11 Requires Manual Setup!

**In Laravel 11, API routes are NOT enabled by default.** You must register them manually.

### Step 1: Enable API Routes in `bootstrap/app.php`

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',          // 👈 Add this line!
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

**What this line does:**
```php
api: __DIR__.'/../routes/api.php',
```

| Without This Line | With This Line |
|-------------------|----------------|
| ❌ `routes/api.php` is ignored | ✅ `routes/api.php` is loaded |
| ❌ API routes return 404 errors | ✅ API routes work correctly |
| ❌ No `/api` prefix | ✅ Automatic `/api` prefix |
| ❌ No API middleware | ✅ API middleware applied |

---

### Step 2: Create `routes/api.php`

Once registered in `bootstrap/app.php`, create your API routes file:

```php
<?php
// routes/api.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaitlistController;

Route::post('/register', [UserController::class, 'register']);
Route::get('/users', [UserController::class, 'getUsers']);

// Waitlist signup endpoint for marketing site
Route::post('/waitlist', [WaitlistController::class, 'store']);
```

**These routes automatically become:**
- `/api/register` (not just `/register`)
- `/api/users` (not just `/users`)
- `/api/waitlist` (not just `/waitlist`)

---

### Why Laravel 11 Changed This

**Before Laravel 11:**
- ✅ API routes worked out of the box
- ❌ Everyone got API setup even if they didn't need it
- ❌ Unnecessary files for simple websites

**Laravel 11 and After:**
- ✅ Cleaner default installation
- ✅ Only add what you need
- ✅ More flexible configuration
- ⚠️ Must manually enable API routes

**Think of it like:** Building a house - you only add the rooms (features) you need!

---

### Common Mistake: Forgetting to Register

```php
// ❌ COMMON MISTAKE
// You create routes/api.php with routes...
Route::post('/register', [UserController::class, 'register']);

// ...but forget to add this to bootstrap/app.php:
api: __DIR__.'/../routes/api.php',

// Result: 404 errors when calling /api/register
```

**Always remember these 2 steps:**
1. ✅ Add `api: __DIR__.'/../routes/api.php'` to `bootstrap/app.php`
2. ✅ Create `routes/api.php` with your routes

---

### Verify It's Working

```bash
# List all routes (should see your API routes)
php artisan route:list

# Filter to show only API routes
php artisan route:list --path=api
```

**Expected output:**
```
POST       api/register ................ UserController@register
GET|HEAD   api/users ................... UserController@getUsers
```

If you don't see them, check that `bootstrap/app.php` has the `api` line!

---

## 🌐 Why Separate API Routes?

Laravel has two main routing files:

| File | Purpose | URL Prefix | Returns |
|------|---------|------------|---------|
| `routes/web.php` | Web pages for browsers | None | HTML views |
| `routes/api.php` | API endpoints for apps | `/api/` | JSON data |

### Why This Separation?

**Web Routes** (`routes/web.php`):
```php
Route::get('/users', [UserController::class, 'showUsers']);
// URL: http://yoursite.com/users
// Returns: HTML page that humans can see
```

**API Routes** (`routes/api.php`):
```php
Route::get('/users', [UserController::class, 'getUsers']);
// URL: http://yoursite.com/api/users
// Returns: JSON data for apps to consume
```

**Why not just use one?**
- 🎯 **Clarity**: Immediately know if a route is for humans or machines
- 🔒 **Security**: Different authentication for web vs API
- 📱 **Mobile Apps**: APIs don't need HTML overhead
- 🔄 **Reusability**: Multiple frontends (web, mobile, desktop) can use same API

---

## 📚 Route Basics

### Anatomy of a Route

```php
Route::post('/register', [UserController::class, 'register']);
```

Let's break this down:

```php
Route::                    // Laravel's routing system
      post                 // HTTP method (GET, POST, PUT, DELETE)
          ('/register',    // The URL path (becomes /api/register)
           [UserController::class, 'register']  // Controller and method to call
          );
```

---

## 🔄 HTTP Methods

HTTP methods define the **action** you want to perform:

| Method | Action | Example | Why Use It? |
|--------|--------|---------|-------------|
| `GET` | Read/Retrieve | Get list of users | Safe, can be cached, no side effects |
| `POST` | Create | Register new user | Creates new resource, not idempotent |
| `PUT` | Update (full) | Replace entire user | Updates entire resource |
| `PATCH` | Update (partial) | Update just email | Updates part of resource |
| `DELETE` | Delete | Remove a user | Removes a resource |

### Why Not Use POST for Everything?

```php
// ❌ BAD - Everything is POST
Route::post('/get-users', ...);      // Should be GET
Route::post('/create-user', ...);    // OK
Route::post('/update-user', ...);    // Should be PUT/PATCH
Route::post('/delete-user', ...);    // Should be DELETE
```

**Problems:**
- 🤷 Can't tell what each endpoint does
- 📦 Browsers can't cache GET requests
- 🔄 Can't bookmark or share links
- 🐛 Harder to debug
- 📖 Not RESTful (industry standard)

```php
// ✅ GOOD - Use appropriate methods
Route::get('/users', ...);           // Retrieve users
Route::post('/users', ...);          // Create user
Route::put('/users/{id}', ...);      // Update user
Route::delete('/users/{id}', ...);   // Delete user
```

**Benefits:**
- ✅ Clear intent from method name
- ✅ Follows REST standards
- ✅ Easier for other developers to understand
- ✅ Better caching and performance

---

## 📝 Our Routes Explained

### File: `routes/api.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| 
| These routes are automatically prefixed with '/api'
| and have middleware applied for API-specific features.
|
*/

// POST /api/register - Create a new user
Route::post('/register', [UserController::class, 'register']);

// GET /api/users - Get all users
Route::get('/users', [UserController::class, 'getUsers']);
```

---

### Route 1: User Registration

```php
Route::post('/register', [UserController::class, 'register']);
```

**Full URL:** `http://yoursite.com/api/register`

**Why POST?**
- ✅ Creating a new resource (user)
- ✅ Sending sensitive data (password)
- ✅ Changes server state (adds to database)
- ✅ Not idempotent (calling twice creates two users)

**Request Example:**
```bash
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123"
  }'
```

**Response:**
```json
{
  "success": true,
  "message": "User registered successfully!",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2026-01-04T07:10:14.000000Z"
  }
}
```

---

### Route 2: Get All Users

```php
Route::get('/users', [UserController::class, 'getUsers']);
```

**Full URL:** `http://yoursite.com/api/users`

**Why GET?**
- ✅ Only retrieving data (not changing anything)
- ✅ Safe to call multiple times
- ✅ Idempotent (same result every time)
- ✅ Can be cached by browsers
- ✅ Can be bookmarked

**Request Example:**
```bash
curl -X GET http://127.0.0.1:8000/api/users
```

**Response:**
```json
{
  "success": true,
  "count": 3,
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "created_at": "2026-01-04T07:10:14.000000Z"
    },
    {
      "id": 2,
      "name": "Jane Smith",
      "email": "jane@example.com",
      "created_at": "2026-01-04T07:15:22.000000Z"
    }
  ]
}
```

---

## 🎯 Route Parameters

### What Are Route Parameters?

Parameters let you capture values from the URL:

```php
// Get a specific user by ID
Route::get('/users/{id}', [UserController::class, 'show']);
```

**Examples:**
- `/api/users/1` → Get user with ID 1
- `/api/users/42` → Get user with ID 42

**Controller receives the parameter:**
```php
public function show($id)
{
    $user = User::findOrFail($id);  // $id contains 1 or 42
    return response()->json($user);
}
```

---

### Optional Parameters

```php
Route::get('/users/{id?}', [UserController::class, 'show']);
```

The `?` makes the parameter optional:
- `/api/users` → Works (no ID)
- `/api/users/5` → Works (ID is 5)

---

### Multiple Parameters

```php
Route::get('/users/{userId}/posts/{postId}', [PostController::class, 'show']);
```

- `/api/users/5/posts/10` → User 5's post 10

---

### Constraints

Limit what values parameters can have:

```php
// Only allow numbers
Route::get('/users/{id}', [UserController::class, 'show'])
     ->where('id', '[0-9]+');

// Only allow letters
Route::get('/users/{name}', [UserController::class, 'findByName'])
     ->where('name', '[A-Za-z]+');
```

---

## 🏗️ Route Organization

### Grouping Routes

When you have many routes, group them:

```php
// All these routes share common attributes
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);        // GET /api/users
    Route::post('/', [UserController::class, 'store']);       // POST /api/users
    Route::get('/{id}', [UserController::class, 'show']);     // GET /api/users/1
    Route::put('/{id}', [UserController::class, 'update']);   // PUT /api/users/1
    Route::delete('/{id}', [UserController::class, 'destroy']); // DELETE /api/users/1
});
```

---

### Resource Routes (Shortcut)

Laravel provides a shortcut for common CRUD operations:

```php
Route::apiResource('users', UserController::class);
```

This **single line** creates these routes automatically:

| Method | URI | Action | Controller Method |
|--------|-----|--------|-------------------|
| GET | /api/users | List all | index() |
| POST | /api/users | Create new | store() |
| GET | /api/users/{id} | Show one | show() |
| PUT/PATCH | /api/users/{id} | Update | update() |
| DELETE | /api/users/{id} | Delete | destroy() |

**Why use it?**
- ✅ Less code to write
- ✅ Follows conventions
- ✅ Easier to remember
- ✅ Consistent naming

---

## 🔒 Middleware

Middleware runs before your controller:

```php
Route::post('/admin/users', [UserController::class, 'store'])
     ->middleware('auth');  // User must be logged in
```

**Common middleware:**
- `auth` - User must be authenticated
- `guest` - User must NOT be logged in
- `throttle` - Limit request rate (prevent spam)
- `verified` - Email must be verified

**Example with rate limiting:**
```php
Route::post('/register', [UserController::class, 'register'])
     ->middleware('throttle:5,1');  // Max 5 requests per minute
```

---

## ✅ Best Practices

### 1. Use RESTful Conventions

```php
// ✅ GOOD - RESTful
Route::get('/users', ...);              // Get all
Route::get('/users/{id}', ...);         // Get one
Route::post('/users', ...);             // Create
Route::put('/users/{id}', ...);         // Update
Route::delete('/users/{id}', ...);      // Delete

// ❌ BAD - Not RESTful
Route::get('/get-all-users', ...);
Route::post('/create-user', ...);
Route::post('/delete-user-by-id', ...);
```

---

### 2. Use Plural Nouns

```php
// ✅ GOOD
Route::get('/users', ...);
Route::get('/products', ...);

// ❌ BAD
Route::get('/user', ...);
Route::get('/product', ...);
```

---

### 3. Avoid Verbs in URLs

```php
// ✅ GOOD - Use HTTP method to indicate action
Route::post('/users', ...);        // Method says "create"
Route::delete('/users/{id}', ...); // Method says "delete"

// ❌ BAD - Redundant verbs
Route::post('/create-user', ...);
Route::post('/delete-user', ...);
```

---

### 4. Version Your API

```php
// For future changes, version your API
Route::prefix('v1')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
});

// URLs become: /api/v1/register
```

**Why?**
- ✅ Can release breaking changes without breaking old apps
- ✅ Mobile apps can't force updates
- ✅ Gradual migration for clients

---

### 5. Keep URLs Simple and Predictable

```php
// ✅ GOOD - Clear hierarchy
/api/users                    // All users
/api/users/5                  // User 5
/api/users/5/posts            // User 5's posts
/api/users/5/posts/10         // User 5's post 10

// ❌ BAD - Confusing
/api/get-user-by-id/5
/api/user_posts?user=5
/api/fetch-specific-post?uid=5&pid=10
```

---

## 📊 Testing Routes

### List All Routes

```bash
php artisan route:list
```

**Output:**
```
POST       api/register ................ UserController@register
GET|HEAD   api/users ................... UserController@getUsers
```

---

### Filter Routes

```bash
# Show only API routes
php artisan route:list --path=api

# Show only routes for UserController
php artisan route:list --name=user
```

---

## 🧪 Testing Routes with cURL

### POST Request
```bash
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test","email":"test@example.com","password":"password123"}'
```

### GET Request
```bash
curl -X GET http://127.0.0.1:8000/api/users
```

### With Parameters
```bash
curl -X GET http://127.0.0.1:8000/api/users/5
```

---

## 🔗 Related Documentation

- [Controllers](./controllers.md) - What happens after a route matches
- [Validation](./validation.md) - Validating incoming request data
- [JSON Responses](./responses.md) - Returning data from controllers

---

**Next Steps:** Learn about [Controllers](./controllers.md) to understand what happens after a route is matched.

---

*Last Updated: January 4, 2026*
