# 📚 Documentation Index

Quick navigation to all documentation files.

---

## 🎯 Getting Started

Start here if you're new to Laravel:

1. **[Main README](./README.md)** - Overview and quick start guide
2. **[MVC Pattern](./architecture/mvc-pattern.md)** - Understanding Laravel's architecture
3. **[Project Structure](./architecture/project-structure.md)** - Where everything lives

---

## 🏗️ Architecture

Understanding how everything fits together:

| Document | Description | Level |
|----------|-------------|-------|
| [MVC Pattern](./architecture/mvc-pattern.md) | Models, Views, Controllers explained | Beginner |
| [Application Flow](./architecture/application-flow.md) | Request lifecycle | Beginner |
| [Project Structure](./architecture/project-structure.md) | Folder organization | Beginner |

---

## 🗄️ Database

Working with data:

| Document | Description | Level |
|----------|-------------|-------|
| [Database Configuration](./database/configuration.md) | Setting up database connection | Beginner |
| [Migrations](./database/migrations.md) | Database version control | Beginner |
| [Models (Eloquent)](./database/models.md) | Working with database as objects | Intermediate |

---

## 🔌 API Development

Building REST APIs:

| Document | Description | Level |
|----------|-------------|-------|
| [Routing](./api/routing.md) | Defining API endpoints | Beginner |
| [Controllers](./api/controllers.md) | Business logic layer | Beginner |
| [Validation](./api/validation.md) | Data validation & security | Intermediate |
| [JSON Responses](./api/responses.md) | Formatting API responses | Beginner |

---

## 🎨 Frontend

Building user interfaces:

| Document | Description | Level |
|----------|-------------|-------|
| [Blade Templates](./frontend/blade-templates.md) | Laravel's templating engine | Beginner |
| [Users View](./frontend/users-view.md) | Building the users display page | Beginner |
| [Waitlist Admin View](./frontend/blade-templates.md#our-waitlist-admin-view) | Internal view of waitlist signups | Beginner |

---

## 🚀 Deployment

| Document | Description | Level |
|----------|-------------|-------|
| [Deploying to DigitalOcean Droplet](./deployment-laravel-droplet.md) | Step-by-step guide to go live | Intermediate |

---

## 🧪 Testing

Ensuring quality:

| Document | Description | Level |
|----------|-------------|-------|
| [API Testing](./testing/api-testing.md) | Testing APIs with tools | Beginner |
| [Browser Testing](./testing/browser-testing.md) | Testing web pages | Beginner |

---

## 📖 Learning Paths

### Path 1: Backend API Developer

Perfect if you're building APIs for mobile apps or frontend frameworks:

1. [MVC Pattern](./architecture/mvc-pattern.md)
2. [Database Configuration](./database/configuration.md)
3. [Migrations](./database/migrations.md)
4. [Models](./database/models.md)
5. [Routing](./api/routing.md)
6. [Controllers](./api/controllers.md)
7. [Validation](./api/validation.md)
8. [Waitlist API Endpoint](./api/routing.md#setting-up-api-routes)
8. [API Testing](./testing/api-testing.md)

---

### Path 2: Full Stack Developer

Perfect if you're building complete web applications:

1. [MVC Pattern](./architecture/mvc-pattern.md)
2. [Database Configuration](./database/configuration.md)
3. [Migrations](./database/migrations.md)
4. [Models](./database/models.md)
5. [Routing](./api/routing.md)
6. [Controllers](./api/controllers.md)
7. [Blade Templates](./frontend/blade-templates.md)
8. [Users View](./frontend/users-view.md)
9. [Waitlist Admin View](./frontend/blade-templates.md#our-waitlist-admin-view)
10. [Validation](./api/validation.md)

---

### Path 3: Database Focus

Perfect if you want to master data management:

1. [Database Configuration](./database/configuration.md)
2. [Migrations](./database/migrations.md)
3. [Models](./database/models.md)
4. [Validation](./api/validation.md)

---

## 🔍 Quick Reference

### Common Tasks

**Create a new migration:**
```bash
php artisan make:migration create_posts_table
```
📖 Learn more: [Migrations](./database/migrations.md)

---

**Create a new controller:**
```bash
php artisan make:controller PostController
```
📖 Learn more: [Controllers](./api/controllers.md)

---

**Create a new model:**
```bash
php artisan make:model Post
```
📖 Learn more: [Models](./database/models.md)

---

**Define a route:**
```php
Route::get('/posts', [PostController::class, 'index']);
```
📖 Learn more: [Routing](./api/routing.md)

---

**Validate request data:**
```php
$request->validate([
    'title' => 'required|string|max:255',
    'content' => 'required',
]);
```
📖 Learn more: [Validation](./api/validation.md)

---

**Return JSON response:**
```php
return response()->json([
    'success' => true,
    'data' => $data
], 200);
```
📖 Learn more: [JSON Responses](./api/responses.md)

---

## 🆘 Troubleshooting

### Database Connection Issues
See: [Database Configuration](./database/configuration.md#troubleshooting)

### Migration Errors
See: [Migrations](./database/migrations.md#common-errors)

### Validation Not Working
See: [Validation](./api/validation.md#debugging)

### Routes Not Found
See: [Routing](./api/routing.md#troubleshooting)

---

## 📝 Contributing to Documentation

When adding new features, remember to:

1. Create a new markdown file in the appropriate folder
2. Update this index file
3. Add internal links to related documentation
4. Include:
   - Clear explanations
   - Code examples
   - Why vs How explanations
   - Common mistakes section
   - Best practices section

---

## 🔗 External Resources

### Official Laravel Documentation
- [Laravel Docs](https://laravel.com/docs)
- [Laracasts](https://laracasts.com) - Video tutorials

### Community
- [Laravel News](https://laravel-news.com)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/laravel)

---

**Last Updated: January 4, 2026**

---

[← Back to Main README](./README.md)
