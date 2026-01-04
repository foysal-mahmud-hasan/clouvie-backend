# 🗄️ Database Migrations

> **Version control for your database schema**

---

## 📋 Table of Contents
- [What are Migrations?](#what-are-migrations)
- [Why Use Migrations?](#why-use-migrations)
- [Migration Structure](#migration-structure)
- [Our Users Table Migration](#our-users-table-migration)
- [Our Waitlist Entries Migration](#our-waitlist-entries-migration)
- [Common Commands](#common-commands)
- [Column Types](#column-types)
- [Best Practices](#best-practices)

---

## 🤔 What are Migrations?

**Migrations** are like **Git for your database**. They track changes to your database structure over time.

```
Instead of manually creating tables in MySQL:
❌ CREATE TABLE users (...)

You write a migration file:
✅ php artisan make:migration create_users_table
```

---

## 💡 Why Use Migrations?

### The Old Way (Manual SQL)

```sql
-- ❌ Problems with this approach:
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255)
);
```

**Problems:**
- 😵 Hard to share with team
- 🔄 No version history
- 🐛 Teammates might have different schemas
- 🚀 Deploying to production is risky
- ⏮️ Can't easily rollback changes

---

### The Laravel Way (Migrations)

```php
// ✅ Benefits of migrations:
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->timestamps();
});
```

**Benefits:**
- ✅ **Version Control**: Track all changes in Git
- ✅ **Teamwork**: Everyone has same schema
- ✅ **Rollback**: Undo changes easily
- ✅ **Documentation**: Schema is code
- ✅ **Consistency**: Same on dev, staging, production
- ✅ **Database Agnostic**: Works with MySQL, PostgreSQL, SQLite

---

## 🏗️ Migration Structure

### File Location
```
database/
  migrations/
    0001_01_01_000000_create_users_table.php    ← Users table
    0001_01_01_000001_create_cache_table.php
    2026_01_04_143022_add_phone_to_users.php    ← Future changes
```

**File Naming:**
- Format: `YYYY_MM_DD_HHMMSS_description.php`
- Timestamp: Determines order of execution
- Description: What the migration does

---

### Basic Structure

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run when migrating "up" (creating/adding)
     */
    public function up(): void
    {
        Schema::create('table_name', function (Blueprint $table) {
            // Define columns here
        });
    }

    /**
     * Run when migrating "down" (reverting/removing)
     */
    public function down(): void
    {
        Schema::dropIfExists('table_name');
    }
};
```

---

## 📝 Our Users Table Migration

### File: `database/migrations/0001_01_01_000000_create_users_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the users table
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Drop the users table
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

---

## 📝 Our Waitlist Entries Migration

### File: `database/migrations/2026_01_04_000000_create_waitlist_entries_table.php`

```php
Schema::create('waitlist_entries', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('monthly_revenue_range')->nullable();
    $table->timestamps();
});
```

**Purpose:**
- Store **waitlist signups** from the marketing site
- Keep things lightweight: just name, email, and optional revenue range
- No password or authentication data – this table is for **internal marketing and future outreach only**

**Key points:**
- `id()` creates an auto-incrementing primary key (same as `BIGINT UNSIGNED AUTO_INCREMENT`)
- `email` is unique so the same address cannot join the waitlist twice
- `monthly_revenue_range` is nullable because the form field is optional


---

### Column Breakdown

#### 1. `$table->id()`

```php
$table->id();
```

**What it creates:**
```sql
id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
```

**Explanation:**
- Creates an auto-incrementing ID column
- Type: Big Integer (can store huge numbers)
- Primary Key (unique identifier for each row)
- Unsigned (only positive numbers)

**Why use it?**
- ✅ Every table needs a unique identifier
- ✅ Auto-increments: 1, 2, 3, 4...
- ✅ Used in relationships (foreign keys)

---

#### 2. `$table->string('name')`

```php
$table->string('name');
```

**What it creates:**
```sql
name VARCHAR(255) NOT NULL
```

**Explanation:**
- Creates a text column
- Maximum length: 255 characters (default)
- NOT NULL: Must have a value
- Perfect for names, titles, short text

**Variations:**
```php
$table->string('name', 100);      // Max 100 chars
$table->string('name')->nullable(); // Can be NULL
```

---

#### 3. `$table->string('email')->unique()`

```php
$table->string('email')->unique();
```

**What it creates:**
```sql
email VARCHAR(255) UNIQUE NOT NULL
```

**Explanation:**
- Text column with max 255 chars
- UNIQUE constraint: No two users can have same email
- Database enforces this rule

**Why unique?**
- 🔒 One account per email address
- 🛡️ Prevents duplicate registrations
- ✅ Database-level validation

---

#### 4. `$table->timestamp('email_verified_at')->nullable()`

```php
$table->timestamp('email_verified_at')->nullable();
```

**What it creates:**
```sql
email_verified_at TIMESTAMP NULL
```

**Explanation:**
- Stores date and time
- Nullable: Can be NULL (not verified yet)
- Used for email verification feature

**How it's used:**
```php
// Not verified
$user->email_verified_at = null;

// Verified
$user->email_verified_at = now();  // Current timestamp
```

---

#### 5. `$table->string('password')`

```php
$table->string('password');
```

**What it creates:**
```sql
password VARCHAR(255) NOT NULL
```

**Explanation:**
- Stores hashed password
- 255 chars enough for bcrypt/argon hash
- Never stores plain text!

**Example values:**
```
Plain: "password123"
Hashed: "$2y$12$vXpN2xJ.../Q7kL..."  ← This gets stored
```

---

#### 6. `$table->rememberToken()`

```php
$table->rememberToken();
```

**What it creates:**
```sql
remember_token VARCHAR(100) NULL
```

**Explanation:**
- Used for "Remember Me" functionality
- Stores a random token
- Laravel handles this automatically

**How it works:**
1. User checks "Remember Me" at login
2. Laravel generates random token
3. Token stored in database and browser cookie
4. User stays logged in for weeks

---

#### 7. `$table->timestamps()`

```php
$table->timestamps();
```

**What it creates:**
```sql
created_at TIMESTAMP NULL
updated_at TIMESTAMP NULL
```

**Explanation:**
- Creates two columns automatically
- `created_at`: When row was created
- `updated_at`: When row was last modified
- Laravel automatically manages these

**Example:**
```php
$user = User::create(['name' => 'John']);
// created_at = 2026-01-04 10:30:15
// updated_at = 2026-01-04 10:30:15

$user->update(['name' => 'Jane']);
// created_at = 2026-01-04 10:30:15 (unchanged)
// updated_at = 2026-01-04 14:25:30 (updated!)
```

---

## 🎯 Common Commands

### Create a Migration

```bash
php artisan make:migration create_posts_table
```

Creates: `database/migrations/2026_01_04_143022_create_posts_table.php`

---

### Run Migrations

```bash
php artisan migrate
```

**What it does:**
- Runs all pending migrations
- Creates `migrations` table to track what's been run
- Executes `up()` methods

**Output:**
```
Migrating: 0001_01_01_000000_create_users_table
Migrated:  0001_01_01_000000_create_users_table (45.23ms)
```

---

### Rollback Last Migration

```bash
php artisan migrate:rollback
```

**What it does:**
- Undoes the last batch of migrations
- Executes `down()` methods

---

### Rollback All Migrations

```bash
php artisan migrate:reset
```

**What it does:**
- Rolls back ALL migrations
- Database returns to empty state

---

### Refresh Database

```bash
php artisan migrate:refresh
```

**What it does:**
1. Rollback all migrations
2. Run all migrations again

**Equivalent to:**
```bash
php artisan migrate:reset
php artisan migrate
```

⚠️ **Warning:** Destroys all data!

---

### Fresh Migration

```bash
php artisan migrate:fresh
```

**What it does:**
1. Drops all tables
2. Runs all migrations

**Difference from refresh:**
- Faster (doesn't run down() methods)
- More dangerous (no rollback logic used)

⚠️ **Warning:** Destroys all data!

---

### Check Migration Status

```bash
php artisan migrate:status
```

**Output:**
```
Migration name .................................. Batch / Status
0001_01_01_000000_create_users_table ............ [1] Ran
0001_01_01_000001_create_cache_table ............ [1] Ran
2026_01_04_143022_add_phone_to_users ............ Pending
```

---

## 📊 Column Types Reference

### Numeric Types

```php
$table->integer('votes');           // INT
$table->bigInteger('views');        // BIGINT
$table->tinyInteger('status');      // TINYINT (0-255)
$table->decimal('price', 8, 2);     // DECIMAL(8,2) - $999,999.99
$table->float('rating', 8, 2);      // FLOAT
$table->boolean('is_active');       // BOOLEAN (true/false)
```

---

### String Types

```php
$table->string('name');             // VARCHAR(255)
$table->string('code', 10);         // VARCHAR(10)
$table->text('description');        // TEXT - Long text
$table->longText('content');        // LONGTEXT - Very long
$table->char('code', 4);            // CHAR(4) - Fixed length
```

---

### Date & Time Types

```php
$table->date('birth_date');         // DATE
$table->time('alarm_time');         // TIME
$table->datetime('scheduled_at');   // DATETIME
$table->timestamp('verified_at');   // TIMESTAMP
$table->timestamps();               // created_at & updated_at
```

---

### Special Types

```php
$table->id();                       // Auto-increment ID
$table->uuid();                     // UUID primary key
$table->json('options');            // JSON column
$table->enum('status', ['pending', 'active']); // ENUM
```

---

## 🔧 Column Modifiers

### Nullable

```php
$table->string('middle_name')->nullable();
```
Allows NULL values.

---

### Default Value

```php
$table->boolean('is_active')->default(true);
$table->integer('views')->default(0);
```
Sets default value if none provided.

---

### Unique

```php
$table->string('username')->unique();
```
Enforces uniqueness (no duplicates).

---

### Index

```php
$table->string('email')->index();
```
Creates index for faster searches.

---

### Unsigned

```php
$table->integer('user_id')->unsigned();
```
Only positive numbers (no negatives).

---

### After

```php
$table->string('phone')->after('email');
```
Places column after another column.

---

## 🔗 Foreign Keys

### Creating Relationships

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();  // References users.id
    $table->string('title');
    $table->text('content');
    $table->timestamps();
});
```

**What `foreignId()->constrained()` does:**
```sql
user_id BIGINT UNSIGNED NOT NULL
FOREIGN KEY (user_id) REFERENCES users(id)
```

**Benefits:**
- ✅ Enforces data integrity
- ✅ Can't create post with invalid user_id
- ✅ Can cascade deletes

---

### With Cascade

```php
$table->foreignId('user_id')
      ->constrained()
      ->onDelete('cascade');  // Delete posts when user deleted
```

---

## ✅ Best Practices

### 1. Always Have up() and down()

```php
// ✅ GOOD - Can be rolled back
public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        // ...
    });
}

public function down(): void
{
    Schema::dropIfExists('users');
}

// ❌ BAD - Can't undo
public function down(): void
{
    //
}
```

---

### 2. Never Modify Existing Migrations

```php
// ❌ BAD - Don't edit after running
// File: 2026_01_01_create_users_table.php
$table->string('phone');  // Adding this after migration ran

// ✅ GOOD - Create new migration
php artisan make:migration add_phone_to_users_table
```

**Why?**
- Team members already ran old version
- Production database already migrated
- Creates inconsistencies

---

### 3. Use Descriptive Names

```php
// ✅ GOOD
create_users_table
add_phone_to_users_table
make_email_nullable_on_users_table

// ❌ BAD
update_users
fix_database
new_migration
```

---

### 4. One Purpose Per Migration

```php
// ✅ GOOD - One clear purpose
create_posts_table.php
create_comments_table.php
add_avatar_to_users_table.php

// ❌ BAD - Multiple unrelated changes
update_multiple_tables.php  // Changes users, posts, and comments
```

---

### 5. Use Foreign Keys

```php
// ✅ GOOD - Enforces relationships
$table->foreignId('user_id')->constrained();

// ❌ BAD - Just an integer, no relationship
$table->integer('user_id');
```

---

## 🧪 Testing Migrations

```bash
# Test on fresh database
php artisan migrate:fresh

# Test rollback
php artisan migrate:rollback

# Test re-running
php artisan migrate
```

**Always test:**
- ✅ up() works
- ✅ down() works
- ✅ Can rollback and migrate again

---

## 🔗 Related Documentation

- [Models](./models.md) - Working with the tables you create
- [Database Configuration](./configuration.md) - Setting up database connection

---

**Next Steps:** Learn about [Models](./models.md) to interact with your database tables.

---

*Last Updated: January 4, 2026*
