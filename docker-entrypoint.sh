#!/bin/sh

echo "Starting Absolute Cinema Backend..."

# Wait for database to be ready
echo "Waiting for database connection..."
MAX_ATTEMPTS=30
ATTEMPT=0

until php artisan db:show > /dev/null 2>&1 || [ $ATTEMPT -eq $MAX_ATTEMPTS ]; do
    ATTEMPT=$((ATTEMPT + 1))
    echo "Waiting for database... attempt $ATTEMPT/$MAX_ATTEMPTS"
    sleep 2
done

if [ $ATTEMPT -eq $MAX_ATTEMPTS ]; then
    echo "WARNING: Could not connect to database after $MAX_ATTEMPTS attempts"
    echo "Continuing anyway..."
fi

# Run migrations and seeding
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Running migrations..."
    php artisan migrate:fresh --force
    
    if [ $? -eq 0 ]; then
        echo "Migrations completed successfully"
        sleep 2
        
        echo "Running seeders..."
        php artisan db:seed --force
        
        if [ $? -eq 0 ]; then
            echo "Seeding completed successfully"
        else
            echo "WARNING: Seeding failed, retrying..."
            sleep 3
            php artisan db:seed --force
        fi
    else
        echo "WARNING: Migrations failed"
    fi
fi

# Clear caches
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Starting services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
