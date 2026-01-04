# 🎮 Controllers - The Logic Layer

> **Controllers handle the "what happens" after a route is matched**

---

## 📋 Table of Contents
- [What is a Controller?](#what-is-a-controller)
- [Why Use Controllers?](#why-use-controllers)
- [Anatomy of a Controller](#anatomy-of-a-controller)
- [Our UserController Explained](#our-usercontroller-explained)
- [Our WaitlistController Explained](#our-waitlistcontroller-explained)
- [Controller Best Practices](#controller-best-practices)
- [Common Patterns](#common-patterns)

---

## 🤔 What is a Controller?

A **Controller** is the "middleman" in your application. It:
1. Receives the request from a route
2. Gets data from the Model (database)
3. Processes that data
4. Returns a response (JSON or View)

```
Route → Controller → Model → Database
                ↓
              Response
```

**Think of it as:** A waiter in a restaurant who takes your order, tells the kitchen, and brings you the food.

---

## 💡 Why Use Controllers?

### ❌ Without Controllers (Routes File Gets Messy)

```php
// ❌ BAD - All logic in routes
Route::post('/register', function(Request $request) {
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8',
    ]);
    
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }
    
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);
    
    return response()->json(['user' => $user], 201);
});

// Imagine 50 more routes like this...
```

**Problems:**
- 😵 Routes file becomes huge
- 🔄 Can't reuse logic
- 🧪 Hard to test
- 📖 Difficult to read

---

### ✅ With Controllers (Clean & Organized)

```php
// ✅ GOOD - Route is simple
Route::post('/register', [UserController::class, 'register']);

// Controller handles all the logic
class UserController extends Controller
{
    public function register(Request $request)
    {
        // All the validation and logic here
    }
}
```

**Benefits:**
- ✅ Routes file stays clean
- ✅ Logic can be reused
- ✅ Easy to test
- ✅ Easy to maintain
- ✅ Follows single responsibility principle

---

## 🏗️ Anatomy of a Controller

### File Location
```
app/
  Http/
    Controllers/
      UserController.php    ← Your controller here
      PostController.php
      CommentController.php
```

---

### Basic Structure

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    // Each method handles one action
    
    public function index()
    {
        // GET /users - Show all users
    }
    
    public function store(Request $request)
    {
        // POST /users - Create new user
    }
    
    public function show($id)
    {
        // GET /users/{id} - Show one user
    }
    
    public function update(Request $request, $id)
    {
        // PUT /users/{id} - Update user
    }
    
    public function destroy($id)
    {
        // DELETE /users/{id} - Delete user
    }
}
```

---

### Method Naming Conventions

For RESTful controllers:

| Method | Route | Purpose | HTTP Method |
|--------|-------|---------|-------------|
| `index()` | /users | List all | GET |
| `create()` | /users/create | Show form | GET |
| `store()` | /users | Create new | POST |
| `show()` | /users/{id} | Show one | GET |
| `edit()` | /users/{id}/edit | Show edit form | GET |
| `update()` | /users/{id} | Update | PUT/PATCH |
| `destroy()` | /users/{id} | Delete | DELETE |

**Why follow this?**
- ✅ Standard convention everyone knows
- ✅ Works with Route::resource() automatically
- ✅ Easier for new developers
- ✅ Self-documenting

---

## 📝 Our UserController Explained

### Full Controller Code

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Register a new user (API)
     */
    public function register(Request $request)
    {
        // Step 1: Validate incoming data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Step 2: Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Step 3: Return success response
            return response()->json([
                'success' => true,
                'message' => 'User registered successfully!',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all users (API)
     */
    public function getUsers()
    {
        try {
            $users = User::select('id', 'name', 'email', 'created_at')
                        ->orderBy('created_at', 'desc')
                        ->get();

            return response()->json([
                'success' => true,
                'count' => $users->count(),
                'data' => $users
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show users page (Web)
     */
    public function showUsers()
    {
        $users = User::select('id', 'name', 'email', 'created_at')
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('users', compact('users'));
    }
}
```

---

## 📝 Our WaitlistController Explained

### File: `app/Http/Controllers/WaitlistController.php`

This controller handles **waitlist signups** for your marketing site.

#### 1. `index()` – Admin Waitlist View (Web)

```php
public function index()
{
    $entries = WaitlistEntry::select('id', 'name', 'email', 'monthly_revenue_range', 'created_at')
        ->orderBy('created_at', 'desc')
        ->get();

    return view('waitlist', compact('entries'));
}
```

- Used by web route: `GET /admin/waitlist`
- Fetches all waitlist entries and passes them to the `waitlist` Blade view
- Perfect for internal/admin use by marketing or product teams

#### 2. `store()` – API Endpoint to Join Waitlist

```php
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:waitlist_entries,email',
        'monthly_revenue_range' => 'nullable|string|max:255',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }

    $entry = WaitlistEntry::create([
        'name' => $request->name,
        'email' => $request->email,
        'monthly_revenue_range' => $request->monthly_revenue_range,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Added to waitlist successfully!',
        'data' => [
            'id' => $entry->id,
            'name' => $entry->name,
            'email' => $entry->email,
            'monthly_revenue_range' => $entry->monthly_revenue_range,
            'created_at' => $entry->created_at,
        ],
    ], 201);
}
```

- Used by API route: `POST /api/waitlist`
- Validates basic marketing information (no password): name, email, optional revenue range
- Stores the data in the `waitlist_entries` table and returns a JSON response


---

## 🔍 Method Breakdown

### Method 1: `register()` - Create New User

**Purpose:** Register a new user via API

**Flow:**
```
1. Receive request with name, email, password
   ↓
2. Validate the data
   ↓
3. If invalid → Return error (422)
   ↓
4. If valid → Hash password and create user
   ↓
5. Return success with user data (201)
```

---

#### Step 1: Validation

```php
$validator = Validator::make($request->all(), [
    'name' => 'required|string|max:255',
    'email' => 'required|string|email|max:255|unique:users',
    'password' => 'required|string|min:8',
]);
```

**Why validate?**
- 🛡️ **Security**: Prevent bad data from entering database
- 🐛 **Data Integrity**: Ensure email format is correct
- 🔒 **Business Rules**: Enforce password length
- 👤 **User Experience**: Give clear error messages

**Validation Rules Explained:**

| Rule | Meaning | Example |
|------|---------|---------|
| `required` | Field must exist | Can't be empty |
| `string` | Must be text | Not a number or object |
| `max:255` | Max length | Can't exceed 255 chars |
| `email` | Valid email format | must have @ and domain |
| `unique:users` | Must be unique in users table | Can't register same email twice |
| `min:8` | Minimum length | Password needs 8+ chars |

---

#### Step 2: Check Validation Result

```php
if ($validator->fails()) {
    return response()->json([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $validator->errors()
    ], 422);
}
```

**HTTP Status 422:** Unprocessable Entity
- Means: "I understood your request, but the data is invalid"
- Used for: Validation errors
- Client should: Fix the data and try again

**Response Example:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

---

#### Step 3: Create User

```php
$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
]);
```

**Why `Hash::make()`?**

```php
// ❌ NEVER do this!
'password' => $request->password  // Stored as plain text "password123"

// ✅ Always do this!
'password' => Hash::make($request->password)  // Stored as "$2y$12$xXx..."
```

**What hashing does:**
- Input: `"password123"`
- Output: `"$2y$12$vXpN2xJ.../Q7kL..."`
- 🔒 One-way encryption (can't reverse it)
- 🛡️ Even if database is stolen, passwords are safe
- ✅ Laravel's `Hash::check()` can verify it later

---

#### Step 4: Return Success Response

```php
return response()->json([
    'success' => true,
    'message' => 'User registered successfully!',
    'data' => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'created_at' => $user->created_at,
    ]
], 201);
```

**HTTP Status 201:** Created
- Means: "New resource was created successfully"
- Used for: POST requests that create something
- Client should: Save the returned ID for future use

**Why not include password?**
- 🔒 Security: Never send passwords back, even hashed
- 📦 Efficiency: Client doesn't need it
- 📖 Best Practice: Sensitive data stays on server

---

#### Step 5: Error Handling

```php
try {
    // Code that might fail
} catch (\Exception $e) {
    return response()->json([
        'success' => false,
        'message' => 'Registration failed',
        'error' => $e->getMessage()
    ], 500);
}
```

**Why try-catch?**
- 🛡️ **Graceful Failure**: App doesn't crash
- 🐛 **Debugging**: You see what went wrong
- 👤 **User Experience**: User gets helpful message
- 🔒 **Security**: Don't expose stack traces in production

**HTTP Status 500:** Internal Server Error
- Means: "Something went wrong on our end"
- Used for: Unexpected errors
- Client should: Try again later or contact support

---

### Method 2: `getUsers()` - Fetch All Users (API)

```php
public function getUsers()
{
    try {
        // Get users, excluding password
        $users = User::select('id', 'name', 'email', 'created_at')
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json([
            'success' => true,
            'count' => $users->count(),
            'data' => $users
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch users',
            'error' => $e->getMessage()
        ], 500);
    }
}
```

**Query Breakdown:**

```php
User::select('id', 'name', 'email', 'created_at')  // Only get these columns
    ->orderBy('created_at', 'desc')                 // Newest first
    ->get();                                         // Execute query
```

**Why `select()` specific columns?**

```php
// ❌ BAD - Gets everything including password
$users = User::all();

// ✅ GOOD - Only what we need
$users = User::select('id', 'name', 'email', 'created_at')->get();
```

**Benefits:**
- 🔒 **Security**: Password not included
- ⚡ **Performance**: Less data transferred
- 📦 **Bandwidth**: Smaller response size
- 🎯 **Clarity**: Clear what data is returned

---

### Method 3: `showUsers()` - Display Users (Web Page)

```php
public function showUsers()
{
    $users = User::select('id', 'name', 'email', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();

    return view('users', compact('users'));
}
```

**Differences from `getUsers()`:**

| Aspect | `getUsers()` (API) | `showUsers()` (Web) |
|--------|-------------------|---------------------|
| Returns | JSON | HTML View |
| For | Mobile apps, frontend frameworks | Web browsers |
| Response Format | `response()->json()` | `view()` |
| Includes Metadata | Yes (success, count) | No (just data) |

**Why separate methods?**
- 🎯 Each has one responsibility
- 🔧 Can evolve independently
- 📱 API might need pagination, web might not
- 🧪 Easier to test

---

## ✅ Controller Best Practices

### 1. Keep Methods Small

```php
// ✅ GOOD - One clear responsibility
public function register(Request $request)
{
    $validated = $this->validateRegistration($request);
    $user = $this->createUser($validated);
    return $this->successResponse($user);
}

// ❌ BAD - Doing too much
public function register(Request $request)
{
    // 200 lines of validation
    // 100 lines of user creation
    // 50 lines of email sending
    // 30 lines of logging
    // 40 lines of response formatting
}
```

**Rule of Thumb:** If a method is more than 30 lines, consider extracting logic.

---

### 2. Use Descriptive Names

```php
// ✅ GOOD - Clear what it does
public function register(Request $request)
public function getActiveUsers()
public function sendWelcomeEmail(User $user)

// ❌ BAD - Vague or unclear
public function doStuff(Request $request)
public function handle()
public function process()
```

---

### 3. Return Consistent Response Format

```php
// ✅ GOOD - Always same structure
return response()->json([
    'success' => true,
    'message' => '...',
    'data' => [...]
], 200);

// ❌ BAD - Inconsistent structure
// Sometimes: return ['user' => $user];
// Other times: return $user;
// Other times: return ['data' => $user];
```

---

### 4. Use Form Requests for Complex Validation

```php
// ✅ GOOD - For complex validation, use Form Request
public function register(RegisterRequest $request)
{
    $user = User::create($request->validated());
    return response()->json(['user' => $user]);
}

// validation logic lives in app/Http/Requests/RegisterRequest.php
```

**When to use Form Requests:**
- More than 5 validation rules
- Custom validation logic needed
- Want to authorize who can make the request

---

### 5. Use Dependency Injection

```php
// ✅ GOOD - Laravel automatically injects dependencies
public function register(Request $request)
{
    // $request is automatically provided by Laravel
}

public function sendEmail(EmailService $emailService, User $user)
{
    // $emailService is automatically injected
    $emailService->send($user);
}

// ❌ BAD - Manual instantiation
public function sendEmail(User $user)
{
    $emailService = new EmailService();  // Hard to test
    $emailService->send($user);
}
```

---

## 🎯 Common Patterns

### Pattern 1: Resource Controller

Full CRUD operations:

```php
class UserController extends Controller
{
    // GET /users
    public function index()
    {
        $users = User::paginate(15);
        return view('users.index', compact('users'));
    }

    // GET /users/create
    public function create()
    {
        return view('users.create');
    }

    // POST /users
    public function store(Request $request)
    {
        $user = User::create($request->validated());
        return redirect()->route('users.show', $user);
    }

    // GET /users/{id}
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    // GET /users/{id}/edit
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    // PUT /users/{id}
    public function update(Request $request, User $user)
    {
        $user->update($request->validated());
        return redirect()->route('users.show', $user);
    }

    // DELETE /users/{id}
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index');
    }
}
```

---

### Pattern 2: API Controller

Returns JSON only:

```php
class ApiUserController extends Controller
{
    public function index()
    {
        return response()->json(User::paginate());
    }

    public function store(Request $request)
    {
        $user = User::create($request->validated());
        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $user->update($request->validated());
        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }
}
```

---

### Pattern 3: Single Action Controller

One controller, one action:

```php
class RegisterUserController extends Controller
{
    public function __invoke(Request $request)
    {
        // All registration logic here
    }
}

// Route:
Route::post('/register', RegisterUserController::class);
```

**When to use:**
- Action is complex and deserves its own file
- Want to keep controllers small
- Action has unique dependencies

---

## 🔗 Related Documentation

- [Routing](./routing.md) - How requests reach controllers
- [Validation](./validation.md) - Validating request data
- [Models](../database/models.md) - Working with database
- [JSON Responses](./responses.md) - Formatting API responses

---

**Next Steps:** Learn about [Validation](./validation.md) to secure your application from bad data.

---

*Last Updated: January 4, 2026*
