#!/bin/sh
set -e  # Exit immediately if a command exits with a non-zero status

# Install dependencies if needed
if [ ! -d "vendor" ]; then
  echo "📦 Installing Composer dependencies..."
  composer install --no-interaction
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

# Make sure storage directories are writable
echo "📁 Setting permissions..."
chmod -R 777 storage bootstrap/cache

# Run migrations and seeding
echo "🗄️ Running migrations..."
php artisan migrate --force --verbose

# Only run passport:install if tables don't exist
# if php artisan db:query "SELECT * FROM oauth_clients WHERE personal_access_client = 1\G" | grep -q 'id'; then
#   echo "🔑 Creating personal access client..."
#   # php artisan passport:install --force
#   php artisan install:api --passport
#   php artisan passport:keys
# else
#   echo "✅ Personal access client already exists"
# fi

echo "🌱 Seeding database..."
php artisan db:seed --force --verbose

# Clear caches
php artisan config:clear
php artisan cache:clear

# Start the server
echo "🚀 Starting Laravel server..."
#php artisan serve --host=0.0.0.0 --port=8000

# Then start PHP-FPM
exec php-fpm
