# 🚀 Quick Reference Guide

> **Fast lookup for common Laravel operations**

---

## 📦 Artisan Commands

### Database

```bash
# Run all pending migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Reset all migrations (DESTROYS DATA!)
php artisan migrate:reset

# Refresh database (reset + migrate)
php artisan migrate:refresh

# Drop all tables and re-run migrations
php artisan migrate:fresh

# Check migration status
php artisan migrate:status
```

---

### Make Commands

```bash
# Create new controller
php artisan make:controller UserController

# Create API controller
php artisan make:controller Api/UserController --api

# Create model
php artisan make:model User

# Create model with migration
php artisan make:model Post -m

# Create model with migration, controller, and factory
php artisan make:model Product -mcf

# Create migration
php artisan make:migration create_posts_table

# Create request (for validation)
php artisan make:request StoreUserRequest

# Create middleware
php artisan make:middleware CheckAge
```

---

### Routes

```bash
# List all routes
php artisan route:list

# List routes for specific path
php artisan route:list --path=api

# List routes for specific controller
php artisan route:list --name=user

# Clear route cache
php artisan route:clear

# Cache routes (production)
php artisan route:cache
```

---

### Cache

```bash
# Clear all caches
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear view cache
php artisan view:clear

# Cache config (production)
php artisan config:cache

# Cache views (production)
php artisan view:cache
```

---

### Development Server

```bash
# Start development server
php artisan serve

# Start on specific port
php artisan serve --port=8080

# Start on specific host
php artisan serve --host=192.168.1.100
```

---

## 🗄️ Eloquent Quick Reference

### Creating Records

```php
// Method 1: Mass assignment
$user = User::create([
    'name' => 'John',
    'email' => 'john@example.com',
    'password' => Hash::make('password'),
]);

// Method 2: New instance
$user = new User();
$user->name = 'John';
$user->email = 'john@example.com';
$user->save();

// Method 3: firstOrCreate (find or create)
$user = User::firstOrCreate(
    ['email' => 'john@example.com'],
    ['name' => 'John', 'password' => Hash::make('password')]
);

// Method 4: updateOrCreate
$user = User::updateOrCreate(
    ['email' => 'john@example.com'],
    ['name' => 'John Doe', 'password' => Hash::make('password')]
);
```

---

### Reading Records

```php
// Get all records
$users = User::all();

// Get first record
$user = User::first();

// Find by ID
$user = User::find(1);
$user = User::findOrFail(1);  // Throws 404 if not found

// Find multiple IDs
$users = User::find([1, 2, 3]);

// Where clauses
$users = User::where('is_active', true)->get();
$users = User::where('name', 'like', '%John%')->get();
$users = User::whereIn('id', [1, 2, 3])->get();
$users = User::whereBetween('age', [18, 65])->get();
$users = User::whereNull('email_verified_at')->get();
$users = User::whereNotNull('email_verified_at')->get();

// Multiple conditions
$users = User::where('is_active', true)
             ->where('email_verified_at', '!=', null)
             ->get();

// Or conditions
$users = User::where('name', 'John')
             ->orWhere('name', 'Jane')
             ->get();

// Ordering
$users = User::orderBy('created_at', 'desc')->get();
$users = User::latest()->get();  // Same as orderBy('created_at', 'desc')
$users = User::oldest()->get();  // Same as orderBy('created_at', 'asc')

// Limiting
$users = User::take(10)->get();
$users = User::limit(10)->get();
$users = User::skip(20)->take(10)->get();  // Offset

// Select specific columns
$users = User::select('id', 'name', 'email')->get();
$users = User::select('name', 'email as user_email')->get();

// Count, sum, avg, max, min
$count = User::count();
$sum = Order::sum('total');
$avg = Product::avg('price');
$max = Product::max('price');
$min = Product::min('price');

// Check existence
$exists = User::where('email', 'john@example.com')->exists();

// Pagination
$users = User::paginate(15);
$users = User::simplePaginate(15);
```

---

### Updating Records

```php
// Method 1: Find and update
$user = User::find(1);
$user->name = 'Jane';
$user->save();

// Method 2: Update method
$user = User::find(1);
$user->update(['name' => 'Jane']);

// Method 3: Mass update
User::where('is_active', false)->update(['status' => 'inactive']);

// Increment/Decrement
$user->increment('login_count');
$user->increment('balance', 100);
$user->decrement('attempts');
$user->decrement('credits', 10);
```

---

### Deleting Records

```php
// Soft delete (if using SoftDeletes trait)
$user = User::find(1);
$user->delete();

// Force delete (permanent)
$user->forceDelete();

// Delete by query
User::where('is_active', false)->delete();

// Restore soft deleted
$user->restore();

// Check if soft deleted
if ($user->trashed()) {
    // User is soft deleted
}

// Include soft deleted in queries
$users = User::withTrashed()->get();
$users = User::onlyTrashed()->get();
```

---

## 🔌 Route Quick Reference

### Basic Routes

```php
// GET route
Route::get('/users', [UserController::class, 'index']);

// POST route
Route::post('/users', [UserController::class, 'store']);

// PUT/PATCH route
Route::put('/users/{id}', [UserController::class, 'update']);
Route::patch('/users/{id}', [UserController::class, 'update']);

// DELETE route
Route::delete('/users/{id}', [UserController::class, 'destroy']);

// Multiple methods
Route::match(['get', 'post'], '/users', [UserController::class, 'index']);

// Any method
Route::any('/users', [UserController::class, 'index']);
```

---

### Route Parameters

```php
// Required parameter
Route::get('/users/{id}', [UserController::class, 'show']);

// Optional parameter
Route::get('/users/{id?}', [UserController::class, 'show']);

// Multiple parameters
Route::get('/posts/{post}/comments/{comment}', [CommentController::class, 'show']);

// Parameter constraints
Route::get('/users/{id}', [UserController::class, 'show'])->where('id', '[0-9]+');
Route::get('/users/{name}', [UserController::class, 'showByName'])->where('name', '[A-Za-z]+');
```

---

### Route Groups

```php
// Prefix
Route::prefix('admin')->group(function () {
    Route::get('/users', [AdminController::class, 'users']);  // /admin/users
    Route::get('/posts', [AdminController::class, 'posts']);  // /admin/posts
});

// Middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/profile', [ProfileController::class, 'show']);
});

// Combined
Route::prefix('admin')
     ->middleware(['auth', 'admin'])
     ->group(function () {
         Route::get('/users', [AdminController::class, 'users']);
     });
```

---

### Resource Routes

```php
// Creates 7 routes automatically
Route::resource('posts', PostController::class);

// API resource (excludes create/edit)
Route::apiResource('posts', PostController::class);

// Only specific actions
Route::resource('posts', PostController::class)->only(['index', 'show']);

// Except specific actions
Route::resource('posts', PostController::class)->except(['destroy']);
```

---

## ✅ Validation Rules

### Common Rules

```php
$request->validate([
    // Required
    'name' => 'required',
    
    // String
    'name' => 'string',
    
    // Email
    'email' => 'email',
    
    // Unique in database
    'email' => 'unique:users',
    'email' => 'unique:users,email',  // Specify column
    'email' => 'unique:users,email,1', // Ignore ID 1
    
    // Exists in database
    'user_id' => 'exists:users,id',
    
    // Numeric
    'age' => 'numeric',
    'age' => 'integer',
    
    // Min/Max
    'password' => 'min:8',
    'password' => 'max:255',
    'age' => 'between:18,65',
    
    // Date
    'birth_date' => 'date',
    'start_date' => 'date|after:today',
    'end_date' => 'date|after:start_date',
    
    // Boolean
    'is_active' => 'boolean',
    
    // Array
    'tags' => 'array',
    'tags.*' => 'string',  // Each element must be string
    
    // File
    'avatar' => 'file',
    'avatar' => 'image',
    'avatar' => 'mimes:jpg,png,gif',
    'avatar' => 'max:2048',  // KB
    
    // In/Not In
    'role' => 'in:admin,user,moderator',
    'status' => 'not_in:banned,suspended',
    
    // Regex
    'phone' => 'regex:/^[0-9]{10}$/',
    
    // URL
    'website' => 'url',
    
    // Confirmed (must have field_confirmation)
    'password' => 'confirmed',  // Needs password_confirmation
    
    // Same as another field
    'password' => 'same:password_confirmation',
    
    // Different from another field
    'new_password' => 'different:old_password',
    
    // Nullable
    'middle_name' => 'nullable|string',
    
    // Sometimes (only validate if present)
    'optional_field' => 'sometimes|required|string',
]);
```

---

### Combining Rules

```php
// Pipe separated
'email' => 'required|string|email|unique:users|max:255',

// Array format
'email' => ['required', 'string', 'email', 'unique:users', 'max:255'],

// With custom messages
$request->validate([
    'email' => 'required|email|unique:users',
], [
    'email.required' => 'Please provide an email address.',
    'email.email' => 'Please provide a valid email.',
    'email.unique' => 'This email is already registered.',
]);
```

---

## 🎨 Blade Quick Reference

### Displaying Data

```blade
{{-- Echo with escaping --}}
{{ $variable }}

{{-- Echo without escaping (dangerous!) --}}
{!! $html !!}

{{-- Default value --}}
{{ $name ?? 'Guest' }}

{{-- Comment (not in HTML) --}}
{{-- This is a comment --}}
```

---

### Control Structures

```blade
{{-- If statements --}}
@if($condition)
    <p>True</p>
@elseif($otherCondition)
    <p>Other</p>
@else
    <p>False</p>
@endif

{{-- Unless --}}
@unless($condition)
    <p>Condition is false</p>
@endunless

{{-- Isset and Empty --}}
@isset($variable)
    <p>Variable is set</p>
@endisset

@empty($array)
    <p>Array is empty</p>
@endempty

{{-- Auth checks --}}
@auth
    <p>Logged in</p>
@endauth

@guest
    <p>Not logged in</p>
@endguest

{{-- Foreach --}}
@foreach($users as $user)
    <p>{{ $user->name }}</p>
@endforeach

{{-- Forelse (with empty case) --}}
@forelse($users as $user)
    <p>{{ $user->name }}</p>
@empty
    <p>No users</p>
@endforelse

{{-- For loop --}}
@for($i = 0; $i < 10; $i++)
    <p>{{ $i }}</p>
@endfor

{{-- While loop --}}
@while($condition)
    <p>Looping...</p>
@endwhile
```

---

### Loop Variables

```blade
@foreach($users as $user)
    {{ $loop->index }}      {{-- 0, 1, 2 --}}
    {{ $loop->iteration }}  {{-- 1, 2, 3 --}}
    {{ $loop->first }}      {{-- true on first --}}
    {{ $loop->last }}       {{-- true on last --}}
    {{ $loop->count }}      {{-- total items --}}
    {{ $loop->remaining }}  {{-- items remaining --}}
@endforeach
```

---

### Template Inheritance

```blade
{{-- Layout (app.blade.php) --}}
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
</head>
<body>
    @yield('content')
    @stack('scripts')
</body>
</html>

{{-- Child view --}}
@extends('layouts.app')

@section('title', 'Page Title')

@section('content')
    <h1>Content here</h1>
@endsection

@push('scripts')
    <script>...</script>
@endpush
```

---

### Components

```blade
{{-- Inline component --}}
<x-alert type="success" message="Saved!" />

{{-- Component with slots --}}
<x-card>
    <x-slot:header>
        Card Header
    </x-slot:header>
    
    Card content here
    
    <x-slot:footer>
        Card Footer
    </x-slot:footer>
</x-card>
```

---

## 🔒 Security

### Password Hashing

```php
use Illuminate\Support\Facades\Hash;

// Hash password
$hashed = Hash::make('password123');

// Verify password
if (Hash::check('password123', $hashedPassword)) {
    // Password matches
}

// Check if needs rehash
if (Hash::needsRehash($hashed)) {
    $hashed = Hash::make('password123');
}
```

---

### CSRF Protection

```blade
{{-- Add to forms --}}
<form method="POST">
    @csrf
    {{-- Form fields --}}
</form>

{{-- For PUT/PATCH/DELETE --}}
<form method="POST">
    @csrf
    @method('PUT')
    {{-- Form fields --}}
</form>
```

---

### XSS Protection

```blade
{{-- Always use {{ }} for user input --}}
{{ $userInput }}  {{-- Safe, auto-escaped --}}

{{-- NEVER use {!! !!} with user input --}}
{!! $trustedHtml !!}  {{-- Only for trusted content --}}
```

---

## 📊 HTTP Status Codes

```php
// Success
200 - OK
201 - Created
204 - No Content

// Client Errors
400 - Bad Request
401 - Unauthorized
403 - Forbidden
404 - Not Found
422 - Unprocessable Entity (Validation Error)
429 - Too Many Requests

// Server Errors
500 - Internal Server Error
503 - Service Unavailable
```

---

## 🔗 Helpful Links

- [Main Documentation](./README.md)
- [Documentation Index](./INDEX.md)
- [Laravel Official Docs](https://laravel.com/docs)

---

*Last Updated: January 4, 2026*
