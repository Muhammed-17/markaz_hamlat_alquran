#!/bin/bash
set -e

# Render injects PORT env var - nginx must listen on it
PORT="${PORT:-80}"
sed -i "s/listen 80;/listen ${PORT};/" /etc/nginx/sites-available/default

cd /var/www/html

# Generate app key if missing (first deploy)
if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY not set. Generating one (set it in Render env vars to persist it)."
    php artisan key:generate --force
fi

# Cache config/routes/views for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations (safe to run every deploy)
php artisan migrate --force

# Create storage symlink
php artisan storage:link || true

# Start PHP-FPM in background, Nginx in foreground
php-fpm -D
nginx -g "daemon off;"