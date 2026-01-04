# 📊 Eloquent Models - Working with Data

> **Turn database rows into PHP objects you can easily work with**

---

## 📋 Table of Contents
- [What is Eloquent?](#what-is-eloquent)
- [Why Use Models?](#why-use-models)
- [Model Structure](#model-structure)
- [CRUD Operations](#crud-operations)
- [Query Builder](#query-builder)
- [Model Properties](#model-properties)
- [Relationships](#relationships)
- [Best Practices](#best-practices)

---

## 🤔 What is Eloquent?

**Eloquent** is Laravel's ORM (Object-Relational Mapping). It lets you work with database records as PHP objects instead of writing SQL.

```php
// ❌ Old way: Raw SQL
$result = mysqli_query($conn, "SELECT * FROM users WHERE id = 1");
$user = mysqli_fetch_assoc($result);
echo $user['name'];

// ✅ Laravel way: Eloquent
$user = User::find(1);
echo $user->name;
```

---

## 💡 Why Use Models?

### Without Models (Raw SQL)

```php
// ❌ Tedious and error-prone
$conn = mysqli_connect('localhost', 'root', 'password', 'database');

$email = mysqli_real_escape_string($conn, $_POST['email']);
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $sql);

if ($result) {
    $user = mysqli_fetch_assoc($result);
    echo $user['name'];
}
```

**Problems:**
- 😵 Too much boilerplate code
- 🐛 SQL injection risks
- 🔄 No code reusability
- 📝 Manual validation
- 🔐 Password hashing forgotten

---

### With Models (Eloquent)

```php
// ✅ Clean and secure
$user = User::where('email', $request->email)->first();
echo $user->name;

// Create user
User::create([
    'name' => 'John',
    'email' => 'john@example.com',
    'password' => Hash::make('password123')
]);
```

**Benefits:**
- ✅ Less code to write
- ✅ SQL injection protected automatically
- ✅ Reusable across application
- ✅ Built-in validation support
- ✅ Relationships made easy

---

## 🏗️ Model Structure

### File Location
```
app/
  Models/
    User.php        ← Your models here
    Post.php
    Comment.php
```

---

### Our User Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

---

### Our WaitlistEntry Model

File: `app/Models/WaitlistEntry.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaitlistEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'monthly_revenue_range',
    ];
}
```

**What this model represents:**
- Each instance is **one person on the marketing waitlist**
- No password/authentication fields – it is for internal reference only
- Safe for lightweight forms that only collect basic contact information


---

## 📝 Model Properties Explained

### 1. `$fillable` - Mass Assignment Protection

```php
protected $fillable = ['name', 'email', 'password'];
```

**What it does:**
- Lists columns that can be mass-assigned
- Protects against unwanted field updates

**Example:**

```php
// ✅ GOOD - Only fillable fields are set
User::create([
    'name' => 'John',
    'email' => 'john@example.com',
    'password' => Hash::make('password'),
]);

// ❌ PREVENTED - is_admin not in $fillable
User::create([
    'name' => 'Hacker',
    'is_admin' => true,  // This will be ignored!
]);
```

**Why needed?**
Imagine a hacker sending this request:
```json
{
  "name": "John",
  "email": "john@example.com",
  "password": "password123",
  "is_admin": true
}
```

Without `$fillable`, they could make themselves an admin!

---

#### Alternative: `$guarded`

```php
// Opposite of $fillable - list what CAN'T be assigned
protected $guarded = ['id', 'is_admin'];

// Everything except these can be mass-assigned
```

**Choose one:**
- Use `$fillable` (whitelist approach) - **recommended**
- Use `$guarded` (blacklist approach)
- Never use both!

---

### 2. `$hidden` - Hide Sensitive Data

```php
protected $hidden = ['password', 'remember_token'];
```

**What it does:**
- Excludes fields from JSON responses
- Protects sensitive information

**Example:**

```php
$user = User::find(1);
return response()->json($user);

// ❌ Without $hidden:
{
  "id": 1,
  "name": "John",
  "email": "john@example.com",
  "password": "$2y$12$...",        // ⚠️ Exposed!
  "remember_token": "abc123..."     // ⚠️ Exposed!
}

// ✅ With $hidden:
{
  "id": 1,
  "name": "John",
  "email": "john@example.com"
  // password and remember_token automatically excluded
}
```

---

### 3. `$casts` - Automatic Type Conversion

```php
protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'settings' => 'array',
    ];
}
```

**What it does:**
- Automatically converts database values to PHP types
- Converts PHP types back to database format

**Examples:**

#### DateTime Casting
```php
// Database stores: "2026-01-04 10:30:15"
$user->email_verified_at;  // Returns Carbon instance

// Can use methods:
$user->email_verified_at->format('M d, Y');  // "Jan 04, 2026"
$user->email_verified_at->diffForHumans();   // "2 hours ago"
```

#### Boolean Casting
```php
// Database stores: 1 or 0
protected $casts = ['is_active' => 'boolean'];

$user->is_active;  // Returns true/false (not 1/0)
```

#### Array Casting
```php
// Database stores: '{"theme":"dark","language":"en"}'
protected $casts = ['settings' => 'array'];

$user->settings;  // Returns: ['theme' => 'dark', 'language' => 'en']
```

#### Password Hashing
```php
protected $casts = ['password' => 'hashed'];

// Automatically hashes on assignment
$user->password = 'password123';
// Stored as: "$2y$12$..."
```

---

## 🔨 CRUD Operations

### Create

#### Method 1: `create()`
```php
$user = User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => Hash::make('password123'),
]);

// Returns created user object
echo $user->id;  // 1
```

#### Method 2: `new` + `save()`
```php
$user = new User();
$user->name = 'John Doe';
$user->email = 'john@example.com';
$user->password = Hash::make('password123');
$user->save();

echo $user->id;  // 1
```

**When to use which?**
- `create()`: When you have all data at once
- `new + save()`: When building object step by step

---

### Read

#### Get One by ID
```php
$user = User::find(1);         // Returns User or null
$user = User::findOrFail(1);   // Returns User or throws 404
```

#### Get First Match
```php
$user = User::where('email', 'john@example.com')->first();
$user = User::where('email', 'john@example.com')->firstOrFail();
```

#### Get All
```php
$users = User::all();  // Returns collection of all users
```

#### Get with Conditions
```php
$activeUsers = User::where('is_active', true)->get();
$admins = User::where('role', 'admin')->get();
```

---

### Update

#### Method 1: Find + Save
```php
$user = User::find(1);
$user->name = 'Jane Doe';
$user->save();
```

#### Method 2: Update Query
```php
User::where('id', 1)->update([
    'name' => 'Jane Doe'
]);
```

#### Method 3: Model Instance Update
```php
$user = User::find(1);
$user->update([
    'name' => 'Jane Doe',
    'email' => 'jane@example.com'
]);
```

---

### Delete

#### Soft Delete (Recommended)
```php
$user = User::find(1);
$user->delete();  // Marks as deleted, doesn't remove from database
```

#### Force Delete
```php
$user = User::find(1);
$user->forceDelete();  // Permanently removes from database
```

#### Delete by Query
```php
User::where('is_active', false)->delete();
```

---

## 🔍 Query Builder

Eloquent provides a fluent interface for building queries:

### Where Clauses

```php
// Simple where
User::where('name', 'John')->get();

// Multiple conditions (AND)
User::where('name', 'John')
    ->where('is_active', true)
    ->get();

// OR conditions
User::where('name', 'John')
    ->orWhere('name', 'Jane')
    ->get();

// Where In
User::whereIn('id', [1, 2, 3])->get();

// Where Between
User::whereBetween('created_at', [$start, $end])->get();

// Where Null
User::whereNull('email_verified_at')->get();

// Where Not Null
User::whereNotNull('email_verified_at')->get();
```

---

### Ordering

```php
// Order by ascending
User::orderBy('created_at')->get();

// Order by descending
User::orderBy('created_at', 'desc')->get();

// Multiple orders
User::orderBy('is_active', 'desc')
    ->orderBy('name', 'asc')
    ->get();

// Latest and Oldest shortcuts
User::latest()->get();   // Same as orderBy('created_at', 'desc')
User::oldest()->get();   // Same as orderBy('created_at', 'asc')
```

---

### Limiting

```php
// Get first 10
User::limit(10)->get();

// Skip 20, take 10 (pagination)
User::skip(20)->take(10)->get();

// Take first
User::first();

// Take first or fail (404)
User::firstOrFail();
```

---

### Selecting Columns

```php
// Select specific columns
User::select('id', 'name', 'email')->get();

// Select with aliases
User::select('name', 'email as user_email')->get();

// Add more columns later
User::select('id', 'name')
    ->addSelect('email')
    ->get();
```

---

### Aggregates

```php
// Count
$count = User::count();                          // 100
$activeCount = User::where('is_active', true)->count();  // 75

// Sum
$totalViews = Post::sum('views');                // 50000

// Average
$avgRating = Review::avg('rating');              // 4.5

// Max and Min
$maxPrice = Product::max('price');               // 999.99
$minPrice = Product::min('price');               // 9.99
```

---

### Pagination

```php
// Paginate results (15 per page)
$users = User::paginate(15);

// In view:
@foreach($users as $user)
    {{ $user->name }}
@endforeach

{{ $users->links() }}  // Pagination links
```

---

## 🔗 Relationships

### One to Many (User has many Posts)

```php
// User.php
public function posts()
{
    return $this->hasMany(Post::class);
}

// Usage:
$user = User::find(1);
$posts = $user->posts;  // Get all posts by this user

foreach($posts as $post) {
    echo $post->title;
}
```

---

### Belongs To (Post belongs to User)

```php
// Post.php
public function user()
{
    return $this->belongsTo(User::class);
}

// Usage:
$post = Post::find(1);
$author = $post->user;  // Get the author
echo $author->name;
```

---

### Many to Many (Users have many Roles)

```php
// User.php
public function roles()
{
    return $this->belongsToMany(Role::class);
}

// Usage:
$user = User::find(1);
$roles = $user->roles;  // Get all roles

// Attach role
$user->roles()->attach($roleId);

// Detach role
$user->roles()->detach($roleId);

// Sync roles (replace all)
$user->roles()->sync([1, 2, 3]);
```

---

## ✅ Best Practices

### 1. Use Descriptive Method Names

```php
// ✅ GOOD - Clear intent
User::whereEmail($email)->first();
User::activeUsers()->get();

// ❌ BAD - Unclear
User::where('a', $x)->first();
```

---

### 2. Use Scopes for Reusable Queries

```php
// In User model:
public function scopeActive($query)
{
    return $query->where('is_active', true);
}

public function scopeVerified($query)
{
    return $query->whereNotNull('email_verified_at');
}

// Usage:
User::active()->get();
User::active()->verified()->get();
```

---

### 3. Always Use Mass Assignment Protection

```php
// ✅ GOOD - Protected
protected $fillable = ['name', 'email', 'password'];

// ❌ BAD - Everything can be assigned
protected $guarded = [];
```

---

### 4. Hide Sensitive Data

```php
// ✅ GOOD
protected $hidden = ['password', 'remember_token', 'api_token'];

// ❌ BAD - Exposes sensitive data
protected $hidden = [];
```

---

### 5. Use Eager Loading to Prevent N+1

```php
// ❌ BAD - N+1 query problem
$users = User::all();
foreach($users as $user) {
    echo $user->posts->count();  // Query for EACH user!
}
// Total: 1 query for users + 100 queries for posts = 101 queries!

// ✅ GOOD - Eager loading
$users = User::with('posts')->get();
foreach($users as $user) {
    echo $user->posts->count();
}
// Total: 1 query for users + 1 query for all posts = 2 queries!
```

---

## 🔗 Related Documentation

- [Migrations](./migrations.md) - Creating database tables
- [Controllers](../api/controllers.md) - Using models in controllers
- [Validation](../api/validation.md) - Validating model data

---

**Next Steps:** Learn about [Validation](../api/validation.md) to ensure data quality.

---

*Last Updated: January 4, 2026*
