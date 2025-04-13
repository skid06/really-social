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
- Docker and Docker Compose (for Docker setup)

## Installation

### Option 1: Docker Setup

```bash
git clone https://github.com/skid06/really-social.git
cd really-social
```

The project includes a Docker Compose configuration for easy setup:

```yaml
version: "3.8"
services:
  backend:
    build: ./backend
    container_name: laravel-backend
    ports:
      - "8000:8000"
    volumes:
      - ./backend:/var/www
    depends_on:
      - mysql
    networks:
      - app-network
    environment:
      DB_HOST: mysql
      DB_PORT: 3306
      DB_DATABASE: laravel_quasar_blog
      DB_USERNAME: laravel
      DB_PASSWORD: secret

  frontend:
    build: ./frontend
    container_name: vue-frontend
    ports:
      - "3000:3000"
    networks:
      - app-network
    stdin_open: true
    tty: true

  mysql:
    image: mysql:8.0
    container_name: mysql-db
    restart: unless-stopped
    ports:
      - "3306:3306"
    environment:
      MYSQL_DATABASE: laravel_quasar_blog
      MYSQL_USER: laravel
      MYSQL_PASSWORD: secret
      MYSQL_ROOT_PASSWORD: secret
      SERVICE_TAGS: dev
      SERVICE_NAME: mysql
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - app-network

volumes:
  mysql_data:

networks:
  app-network:
    driver: bridge
```

Start the application using Docker Compose:

```bash
docker-compose up -d
```

To run database migrations and seeds in Docker:

```bash
# Access the Laravel container
docker exec -it laravel-backend bash

# Inside the container, run migrations and seeds
php artisan migrate --seed

# If you want to run specific seeders
php artisan db:seed --class=UsersTableSeeder
php artisan db:seed --class=BlogsTableSeeder
```

Access the containerized application at:

- Laravel API: http://localhost:8000
- Quasar Frontend: http://localhost:3000
- MySQL Database: localhost:3306 (accessible from host machine)

### Option 2: Manual Setup

#### 1. Clone the repository

```bash
git clone https://github.com/skid06/really-social.git
cd backend
```

#### 2. Set up Laravel Backend

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
# Run migrations with seed data
php artisan migrate --seed
# Install Laravel Passport
php artisan passport:install
# Create a test user (if not using seeders)
php artisan tinker
>>> \App\Models\User::factory()->create(['email' => 'test@example.com', 'password' => bcrypt('password')]);
>>> exit
```

#### 3. Set up Quasar Frontend

```bash
# Navigate to frontend directory
cd frontend
# Install dependencies
npm install
# Build for development
npm run dev
```

#### 4. Run the application

```bash
# In the Laravel root directory (in a separate terminal)
php artisan serve
# In the frontend directory
npm run dev
```

The application will be available at:

- Laravel API: http://localhost:8000
- Quasar Frontend: http://localhost:8080 (or another port if 8080 is in use)

## Database Configuration

### Docker Environment

In the Docker setup, MySQL is preconfigured with the following credentials:

- Database: laravel_quasar_blog
- Username: laravel
- Password: secret
- Root Password: secret

The database data is persisted using Docker volumes.

### Manual Environment

Configure your MySQL database in the `.env` file:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_quasar_blog
DB_USERNAME=root
DB_PASSWORD=your_password
```

## Database Seeding

The application includes database seeders to populate your database with test data:

```bash
# Run all seeders
php artisan db:seed

# Run specific seeders
php artisan db:seed --class=UsersTableSeeder
php artisan db:seed --class=BlogsTableSeeder
```

After seeding, you can log in with the following default credentials:

- Email: admin@example.com
- Password: password

## Usage

1. Login with your test user credentials
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
- **Database**: MySQL 8.0
- **Container**: Docker & Docker Compose

## Project Structure

### Laravel Backend

The Laravel backend follows standard Laravel architecture with:

- Models in `app/Models`
- Controllers in `app/Http/Controllers/API`
- Database migrations in `database/migrations`
- API routes in `routes/api.php`
- Database seeders in `database/seeders`

### Quasar Frontend

The Quasar frontend follows standard Quasar architecture with:

- Pages in `src/pages`
- Components in `src/components`
- Router configuration in `src/router`
- API setup in `src/boot/axios.js`

### Docker Configuration

- Backend Dockerfile in `./backend/Dockerfile`
- Frontend Dockerfile in `./frontend/Dockerfile`
- MySQL database configuration in Docker Compose
- Docker Compose configuration in the root directory
