# Laravel + Quasar Blog Management System

This project combines Laravel (backend API) with Quasar/Vue.js (frontend) to create a blog management system with authentication.

## Features

- Laravel Passport API Authentication
- Blog Management (CRUD operations)
- Blog status management (publish/hide)
- Blog archiving (soft delete)
- Search functionality

## Prerequisites

- PHP 8.1+
- Composer
- Node.js 16+
- npm or yarn
- MySQL

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/your-username/laravel-quasar-blog.git
cd laravel-quasar-blog
```

### 2. Set up Laravel Backend

```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database connection in .env file
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel_quasar_blog
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations
php artisan migrate

# Install Laravel Passport
php artisan passport:install

# Create a test user
php artisan tinker
>>> \App\Models\User::factory()->create(['email' => 'test@example.com', 'password' => bcrypt('password')]);
>>> exit
```

### 3. Set up Quasar Frontend

```bash
# Navigate to frontend directory
cd frontend

# Install dependencies
npm install

# Build for development
npm run dev
```

### 4. Run the application

```bash
# In the Laravel root directory (in a separate terminal)
php artisan serve

# In the frontend directory
npm run dev
```

The application will be available at:
- Laravel API: http://localhost:8000
- Quasar Frontend: http://localhost:8080 (or another port if 8080 is in use)

## Usage

1. Login with your test user credentials:
   - Email: test@example.com
   - Password: password

2. After login, you'll be redirected to the blog management page where you can:
   - View all blogs
   - Search blogs by title
   - Add new blogs
   - Edit existing blogs
   - Publish/Hide blogs
   - Preview blogs
   - Archive blogs (soft delete)

## Tech Stack

- **Backend**: Laravel 12.8.1, Laravel Passport
- **Frontend**: Vue.js 3, Quasar Framework
- **Database**: MySQL

## Project Structure

### Laravel Backend

The Laravel backend follows standard Laravel architecture with:
- Models in `app/Models` 
- Controllers in `app/Http/Controllers/API`
- Database migrations in `database/migrations`
- API routes in `routes/api.php`

### Quasar Frontend

The Quasar frontend follows standard Quasar architecture with:
- Pages in `src/pages`
- Components in `src/components`
- Router configuration in `src/router`
- API setup in `src/boot/axios.js`
