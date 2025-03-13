#!/bin/bash

# Wait for the database to be ready
echo "Waiting for database connection..."
until php -r "
try {
    \$dbh = new PDO('pgsql:host=${DB_HOST};port=${DB_PORT}', '${DB_USERNAME}', '${DB_PASSWORD}');
    echo \"Database connection established\n\";
    exit(0);
} catch(PDOException \$ex) {
    echo \"Waiting for database connection...\n\";
    sleep(1);
}
exit(1);
"
do
    echo "Waiting for database connection..."
    sleep 1
done

cd /var/www/html

# Install dependencies
composer install --no-interaction --optimize-autoloader --no-dev

# Generate application key if not set
php artisan key:generate --no-interaction --force

# Run migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Start Nginx and PHP-FPM
service nginx start
exec php-fpm
