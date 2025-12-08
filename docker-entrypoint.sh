#!/bin/bash
set -e

echo "Fixing permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Clearing cached config..."
php artisan config:clear || true
php artisan cache:clear || true

echo "Running database migrations..."
php artisan migrate --force || echo "Migrations failed or already run"

echo "Creating admin user if not exists..."
php artisan tinker --execute="
if (!\App\Models\Admin::where('email', 'ore@gmail.com')->exists()) {
    \App\Models\Admin::create([
        'firstName' => 'Admin',
        'lastName' => 'User',
        'email' => 'ore@gmail.com',
        'password' => bcrypt('ore123-'),
        'type' => 'Super Admin',
        'status' => 'active'
    ]);
    echo 'Admin user created successfully';
} else {
    echo 'Admin user already exists';
}
" || echo "Admin creation failed"

echo "Starting Apache..."
exec apache2-foreground
