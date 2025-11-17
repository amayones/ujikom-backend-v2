#!/bin/bash

echo "=== Production Seeding Script ==="
echo "Starting database seeding process..."

# Function to check if database is ready
check_database() {
    php artisan db:show 2>/dev/null
    return $?
}

# Wait for database to be ready
echo "Checking database connection..."
MAX_WAIT=30
WAIT_COUNT=0

while [ $WAIT_COUNT -lt $MAX_WAIT ]; do
    if check_database; then
        echo "Database is ready!"
        break
    else
        echo "Waiting for database... ($((WAIT_COUNT + 1))/$MAX_WAIT)"
        sleep 2
        WAIT_COUNT=$((WAIT_COUNT + 1))
    fi
done

if [ $WAIT_COUNT -eq $MAX_WAIT ]; then
    echo "ERROR: Database not ready after $MAX_WAIT attempts"
    exit 1
fi

# Run migrations
echo "Running migrations..."
php artisan migrate:fresh --force

if [ $? -ne 0 ]; then
    echo "ERROR: Migration failed"
    exit 1
fi

echo "Migration completed successfully"
sleep 2

# Run seeders one by one with verification
echo "Starting seeding process..."

SEEDERS=(
    "UserSeeder"
    "StudioSeeder"
    "FilmSeeder"
    "SeatSeeder"
    "PriceSeeder"
    "ScheduleSeeder"
    "OrderSeeder"
)

for SEEDER in "${SEEDERS[@]}"; do
    echo "Running $SEEDER..."
    MAX_RETRIES=3
    RETRY=0
    
    while [ $RETRY -lt $MAX_RETRIES ]; do
        php artisan db:seed --class=$SEEDER --force
        
        if [ $? -eq 0 ]; then
            echo "✓ $SEEDER completed successfully"
            break
        else
            RETRY=$((RETRY + 1))
            if [ $RETRY -lt $MAX_RETRIES ]; then
                echo "⚠ $SEEDER failed, retrying... ($RETRY/$MAX_RETRIES)"
                sleep 3
            else
                echo "✗ $SEEDER failed after $MAX_RETRIES attempts"
                exit 1
            fi
        fi
    done
    
    sleep 1
done

echo ""
echo "=== Seeding Summary ==="
php artisan tinker --execute="
    echo 'Users: ' . \App\Models\User::count() . PHP_EOL;
    echo 'Studios: ' . \App\Models\Studio::count() . PHP_EOL;
    echo 'Films: ' . \App\Models\Film::count() . PHP_EOL;
    echo 'Seats: ' . \App\Models\Seat::count() . PHP_EOL;
    echo 'Prices: ' . \App\Models\Price::count() . PHP_EOL;
    echo 'Schedules: ' . \App\Models\Schedule::count() . PHP_EOL;
    echo 'Orders: ' . \App\Models\Order::count() . PHP_EOL;
"

echo ""
echo "=== Seeding completed successfully ==="
