#!/bin/sh
set -e

# Ensure .env exists
if [ ! -f .env ]; then
  echo "📄 .env not found. Copying from .env.example..."
  cp .env.example .env
fi

# Create necessary Laravel directories
mkdir -p /var/www/bootstrap/cache /var/www/storage
chmod -R 775 /var/www/bootstrap/cache /var/www/storage

# Install dependencies if not already installed
if [ ! -f vendor/autoload.php ]; then
  echo "📦 Installing Composer dependencies..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL..."
ATTEMPTS=0
MAX_ATTEMPTS=30
until mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "SELECT 1" >/dev/null 2>&1; do
  ATTEMPTS=$((ATTEMPTS+1))
  if [ $ATTEMPTS -eq $MAX_ATTEMPTS ]; then
    echo "❌ Could not connect to MySQL after $MAX_ATTEMPTS attempts. Exiting."
    exit 1
  fi
  echo "Waiting for MySQL to be ready... ($ATTEMPTS/$MAX_ATTEMPTS)"
  sleep 2
done

echo "✅ MySQL is up - running Laravel setup"

# Set permissions
echo "📁 Setting permissions..."
chmod -R 777 storage bootstrap/cache

# Check if the migrations table exists before running migrate
echo "🔍 Checking if database is already migrated..."
TABLE_COUNT=$(mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" -D"$DB_DATABASE" -e "SHOW TABLES;" | wc -l)

if [ "$TABLE_COUNT" -le 1 ]; then
  echo "🗄️ No tables found. Running migrations..."
  php artisan migrate --force --verbose

  echo "🌱 Seeding database..."
  php artisan db:seed --force --verbose
else
  echo "✅ Tables already exist. Skipping migrations and seed."
fi

# Clear caches
php artisan config:clear
php artisan cache:clear

# Start PHP-FPM
echo "🚀 Starting Laravel server..."
exec php-fpm
