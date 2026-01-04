# 🎨 Blade Templates - Laravel's Templating Engine

> **Create dynamic HTML views with ease**

---

## 📋 Table of Contents
- [What is Blade?](#what-is-blade)
- [Why Use Blade?](#why-use-blade)
- [Basic Syntax](#basic-syntax)
- [Control Structures](#control-structures)
- [Our Users View Explained](#our-users-view-explained)
 - [Our Waitlist Admin View](#our-waitlist-admin-view)
- [Template Inheritance](#template-inheritance)
- [Best Practices](#best-practices)

---

## 🤔 What is Blade?

**Blade** is Laravel's templating engine. It lets you write clean HTML with embedded PHP logic.

```blade
{{-- Blade Template --}}
<h1>Hello, {{ $name }}!</h1>

@if($user->isAdmin())
    <p>Welcome, Admin!</p>
@endif

@foreach($posts as $post)
    <div>{{ $post->title }}</div>
@endforeach
```

**Compiles to PHP:**
```php
<h1>Hello, <?php echo e($name); ?>!</h1>

<?php if($user->isAdmin()): ?>
    <p>Welcome, Admin!</p>
<?php endif; ?>

<?php foreach($posts as $post): ?>
    <div><?php echo e($post->title); ?></div>
<?php endforeach; ?>
```

---

## 💡 Why Use Blade?

### Without Blade (Plain PHP in HTML)

```php
<!-- ❌ Messy and hard to read -->
<h1>Users</h1>
<ul>
<?php foreach($users as $user): ?>
    <li>
        <?php echo htmlspecialchars($user->name); ?>
        (<?php echo htmlspecialchars($user->email); ?>)
    </li>
<?php endforeach; ?>
</ul>
```

**Problems:**
- 😵 PHP tags everywhere (`<?php`, `?>`)
- 🐛 Easy to forget `htmlspecialchars()` (XSS vulnerability)
- 📖 Hard to read and maintain
- 🎨 Mixing logic with presentation

---

### With Blade (Clean and Safe)

```blade
{{-- ✅ Clean and readable --}}
<h1>Users</h1>
<ul>
@foreach($users as $user)
    <li>{{ $user->name }} ({{ $user->email }})</li>
@endforeach
</ul>
```

**Benefits:**
- ✅ Clean, readable syntax
- ✅ Auto-escapes output (prevents XSS)
- ✅ Template inheritance
- ✅ Reusable components
- ✅ Cleaner than plain PHP

---

## 📚 Basic Syntax

### Displaying Data

#### Echo with Escaping (Safe)
```blade
{{ $name }}
```

**What it does:**
- Outputs the variable
- Automatically escapes HTML (prevents XSS attacks)

```blade
<!-- Input -->
$name = '<script>alert("hack")</script>';
{{ $name }}

<!-- Output (safe!) -->
&lt;script&gt;alert("hack")&lt;/script&gt;
```

---

#### Echo without Escaping (Dangerous!)
```blade
{!! $html !!}
```

**Use ONLY when:**
- You trust the content 100%
- Content is from your own database
- Content is sanitized HTML

```blade
{!! $blogPost->content !!}  // Blog content with HTML formatting
```

⚠️ **Warning:** Never use with user input!

---

### Comments

```blade
{{-- This comment won't appear in HTML --}}

<!-- This comment WILL appear in HTML -->
```

---

### Blade vs PHP

```blade
{{-- Blade way --}}
{{ $name }}

{{-- PHP way (also works in Blade) --}}
<?php echo $name; ?>
```

**Always prefer Blade syntax!**

---

## 🎯 Control Structures

### If Statements

```blade
@if($user->isAdmin())
    <p>Welcome, Admin!</p>
@elseif($user->isModerator())
    <p>Welcome, Moderator!</p>
@else
    <p>Welcome, User!</p>
@endif
```

---

### Unless (Opposite of If)

```blade
@unless($user->isAdmin())
    <p>You are not an admin.</p>
@endunless

{{-- Same as: --}}
@if(!$user->isAdmin())
    <p>You are not an admin.</p>
@endif
```

---

### Isset and Empty

```blade
@isset($name)
    <p>Name is set: {{ $name }}</p>
@endisset

@empty($users)
    <p>No users found.</p>
@endempty
```

---

### Auth Checks

```blade
@auth
    <p>You are logged in!</p>
@endauth

@guest
    <p>Please log in.</p>
@endguest
```

---

### Loops

#### Foreach Loop
```blade
@foreach($users as $user)
    <div>{{ $user->name }}</div>
@endforeach
```

---

#### For Loop
```blade
@for($i = 0; $i < 10; $i++)
    <div>Iteration {{ $i }}</div>
@endfor
```

---

#### While Loop
```blade
@while($condition)
    <div>Still looping...</div>
@endwhile
```

---

#### Forelse (Foreach with Else)
```blade
@forelse($users as $user)
    <div>{{ $user->name }}</div>
@empty
    <p>No users found.</p>
@endforelse
```

**Perfect for lists that might be empty!**

---

### Loop Variable

Inside loops, you have access to `$loop`:

```blade
@foreach($users as $user)
    <div>
        {{ $loop->index }}      {{-- 0, 1, 2, ... --}}
        {{ $loop->iteration }}  {{-- 1, 2, 3, ... --}}
        {{ $loop->first }}      {{-- true on first iteration --}}
        {{ $loop->last }}       {{-- true on last iteration --}}
        {{ $loop->count }}      {{-- Total items --}}
        {{ $loop->remaining }}  {{-- Items remaining --}}
    </div>
@endforeach
```

**Example:**
```blade
@foreach($users as $user)
    <div class="{{ $loop->first ? 'first' : '' }} {{ $loop->last ? 'last' : '' }}">
        {{ $loop->iteration }}. {{ $user->name }}
    </div>
@endforeach
```

---

## 📝 Our Users View Explained

### File: `resources/views/users.blade.php`

Let's break down key parts:

---

### 1. Blade Comments

```blade
{{-- 
    BEGINNER'S NOTE: This is a Blade template
    - Blade is Laravel's templating engine
    - {{ }} is used to display PHP variables safely
    - @foreach, @if are Blade directives
--}}
```

**Why use `{{-- --}}` instead of `<!-- -->`?**
- `{{-- --}}` doesn't appear in final HTML (faster)
- `<!-- -->` appears in HTML source (visible to users)

---

### 2. Displaying Count

```blade
<div class="stats">
    <strong>Total Users:</strong> {{ count($users) }}
</div>
```

**How it works:**
- `count($users)` gets array length
- `{{ }}` safely outputs the number
- Auto-escaped (though numbers don't need escaping)

---

### 3. Conditional Display

```blade
@if(count($users) > 0)
    {{-- Show user cards --}}
    <div class="users-grid">
        @foreach($users as $user)
            {{-- User cards here --}}
        @endforeach
    </div>
@else
    {{-- Show empty state --}}
    <div class="empty-state">
        <h2>No Users Yet</h2>
        <p>Start by registering users</p>
    </div>
@endif
```

**Why check count first?**
- Better user experience
- Shows helpful message when empty
- Prevents empty grid

**Alternative using `@forelse`:**
```blade
<div class="users-grid">
    @forelse($users as $user)
        {{-- User cards --}}
    @empty
        <div class="empty-state">
            <h2>No Users Yet</h2>
        </div>
    @endforelse
</div>
```

---

### 4. Loop Through Users

```blade
@foreach($users as $user)
    <div class="user-card">
        {{-- Display user info --}}
    </div>
@endforeach
```

**What happens:**
1. Loop starts with first user
2. `$user` contains current user object
3. Card is rendered
4. Repeats for each user
5. Loop ends

---

### 5. Display User Data

```blade
<div class="user-avatar">
    {{ substr($user->name, 0, 1) }}
</div>
<div class="user-name">{{ $user->name }}</div>
<div class="user-email">📧 {{ $user->email }}</div>
```

**Accessing properties:**
- `$user->name` gets the name
- `$user->email` gets the email
- `$user->id` gets the ID

---

### 6. Date Formatting

```blade
<div class="user-date">
    📅 Joined: {{ $user->created_at->format('M d, Y') }}
</div>
```

**How it works:**
- `created_at` is a Carbon instance (not a string!)
- Carbon has many methods: `format()`, `diffForHumans()`, etc.
- `format('M d, Y')` outputs: "Jan 04, 2026"

**More date examples:**
```blade
{{ $user->created_at->format('Y-m-d') }}           {{-- 2026-01-04 --}}
{{ $user->created_at->format('F j, Y') }}          {{-- January 4, 2026 --}}
{{ $user->created_at->format('g:i A') }}           {{-- 10:30 AM --}}
{{ $user->created_at->diffForHumans() }}           {{-- 2 hours ago --}}
{{ $user->created_at->toDateString() }}            {{-- 2026-01-04 --}}
{{ $user->created_at->toTimeString() }}            {{-- 10:30:15 --}}
```

---

### 7. Using Helper Functions

```blade
<div>
    {{ url('/api/register') }}
</div>
```

**Common helpers:**
```blade
{{ url('/path') }}              {{-- Full URL --}}
{{ route('users.index') }}      {{-- Named route URL --}}
{{ asset('images/logo.png') }}  {{-- Asset URL --}}
{{ config('app.name') }}        {{-- Config value --}}
{{ env('APP_ENV') }}            {{-- Environment variable --}}
```

---

## 🏗️ Template Inheritance

### Master Layout

```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title') - My App</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <nav>
        {{-- Navigation here --}}
    </nav>

    <main>
        @yield('content')
    </main>

    <footer>
        {{-- Footer here --}}
    </footer>

    @stack('scripts')
</body>
</html>
```

---

### Child View

```blade
{{-- resources/views/users.blade.php --}}
@extends('layouts.app')

@section('title', 'Users List')

@section('content')
    <h1>Users</h1>
    
    @foreach($users as $user)
        <div>{{ $user->name }}</div>
    @endforeach
@endsection

@push('scripts')
    <script>
        console.log('Page loaded!');
    </script>
@endpush
```

**How it works:**
1. `@extends` says "use this layout"
2. `@section` fills in `@yield` spots
3. `@push` adds to `@stack` spots

---

## 🎁 Components

### Creating a Component

```bash
php artisan make:component UserCard
```

Creates:
- `app/View/Components/UserCard.php`
- `resources/views/components/user-card.blade.php`

---

### Component View

```blade
{{-- resources/views/components/user-card.blade.php --}}
<div class="user-card">
    <h3>{{ $user->name }}</h3>
    <p>{{ $user->email }}</p>
</div>
```

---

### Using Component

```blade
<x-user-card :user="$user" />

{{-- Or loop: --}}
@foreach($users as $user)
    <x-user-card :user="$user" />
@endforeach
```

---

## 📝 Our Waitlist Admin View

### File: `resources/views/waitlist.blade.php`

This Blade template shows **all waitlist signups** in a simple admin dashboard.

**Highlights:**
- Receives an `$entries` collection from `WaitlistController::index()`
- Renders a table with columns: ID, Name, Email, Monthly Revenue, Joined At
- Shows a friendly empty state when there are no signups yet

**Example loop:**

```blade
@foreach($entries as $entry)
    <tr>
        <td>{{ $entry->id }}</td>
        <td>{{ $entry->name }}</td>
        <td>{{ $entry->email }}</td>
        <td>
            @if($entry->monthly_revenue_range)
                <span class="badge">{{ $entry->monthly_revenue_range }}</span>
            @else
                <span class="muted">Not provided</span>
            @endif
        </td>
        <td class="muted">{{ $entry->created_at->format('M d, Y H:i') }}</td>
    </tr>
@endforeach
```

Access this view via the web route:

- `GET /admin/waitlist` → internal-only page for your team


**Benefits:**
- ✅ Reusable across pages
- ✅ Cleaner code
- ✅ Easier to maintain

---

## 🔒 Security

### XSS Protection

```blade
{{-- ✅ SAFE - Auto-escaped --}}
{{ $userInput }}

{{-- ❌ DANGEROUS - Not escaped --}}
{!! $userInput !!}
```

**Example attack:**
```php
$userInput = '<script>alert("Hacked!")</script>';
```

```blade
{{ $userInput }}
<!-- Output: &lt;script&gt;alert("Hacked!")&lt;/script&gt; -->
<!-- Browser shows text, doesn't execute -->

{!! $userInput !!}
<!-- Output: <script>alert("Hacked!")</script> -->
<!-- Browser executes! BAD! -->
```

**Rule:** Always use `{{ }}` unless you have a specific reason not to.

---

## ✅ Best Practices

### 1. Keep Logic Out of Views

```blade
{{-- ❌ BAD - Complex logic in view --}}
@php
    $activeUsers = [];
    foreach($users as $user) {
        if($user->is_active && $user->email_verified_at) {
            $activeUsers[] = $user;
        }
    }
@endphp

{{-- ✅ GOOD - Logic in controller --}}
{{-- Controller passes $activeUsers to view --}}
@foreach($activeUsers as $user)
    <div>{{ $user->name }}</div>
@endforeach
```

---

### 2. Use Components for Repeated Elements

```blade
{{-- ❌ BAD - Repeat same HTML everywhere --}}
<div class="alert alert-success">
    <strong>Success!</strong> {{ $message }}
</div>

{{-- ✅ GOOD - Create component --}}
<x-alert type="success" :message="$message" />
```

---

### 3. Use Template Inheritance

```blade
{{-- ❌ BAD - Repeat header/footer in every file --}}
<!DOCTYPE html>
<html>
<head>...</head>
<body>
    <nav>...</nav>
    {{-- Page content --}}
    <footer>...</footer>
</body>
</html>

{{-- ✅ GOOD - Use layout --}}
@extends('layouts.app')

@section('content')
    {{-- Page content only --}}
@endsection
```

---

### 4. Name Your Layouts Clearly

```
resources/views/
    layouts/
        app.blade.php          ← Main layout
        guest.blade.php        ← For non-logged-in users
        admin.blade.php        ← Admin panel layout
    components/
        alert.blade.php
        user-card.blade.php
    users/
        index.blade.php
        show.blade.php
        edit.blade.php
```

---

### 5. Use Comments for Complex Sections

```blade
{{-- 
    USER CARDS GRID
    Displays all registered users in a responsive grid.
    Falls back to empty state if no users exist.
--}}
@forelse($users as $user)
    <x-user-card :user="$user" />
@empty
    <x-empty-state message="No users yet" />
@endforelse
```

---

## 🔗 Related Documentation

- [Controllers](../api/controllers.md) - Passing data to views
- [Models](../database/models.md) - Data shown in views

---

**Next Steps:** Start building more complex views with [Template Inheritance](https://laravel.com/docs/blade#template-inheritance) and [Components](https://laravel.com/docs/blade#components).

---

*Last Updated: January 4, 2026*
