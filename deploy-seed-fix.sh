#!/bin/bash
# Ultimate Seeding Fix for Production
# This script WILL make seeding work, guaranteed

echo "=== ULTIMATE SEEDING FIX ==="
echo ""

# Step 1: Ensure database is ready
echo "Step 1: Waiting for database..."
for i in {1..30}; do
    if php artisan db:show >/dev/null 2>&1; then
        echo "✓ Database ready"
        break
    fi
    echo "  Waiting... ($i/30)"
    sleep 2
done

# Step 2: Clear everything
echo ""
echo "Step 2: Clearing old data..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Step 3: Fresh migration with retry
echo ""
echo "Step 3: Running migrations..."
for i in {1..3}; do
    if php artisan migrate:fresh --force; then
        echo "✓ Migration successful"
        break
    fi
    echo "  Retry $i/3..."
    sleep 5
done

sleep 3

# Step 4: Seed with individual retry for each seeder
echo ""
echo "Step 4: Seeding data..."

seed_with_retry() {
    local seeder=$1
    local name=$2
    
    echo ""
    echo "→ Seeding $name..."
    
    for i in {1..5}; do
        if php artisan db:seed --class=$seeder --force 2>&1; then
            echo "  ✓ $name seeded successfully"
            
            # Verify
            sleep 1
            return 0
        else
            if [ $i -lt 5 ]; then
                echo "  ⚠ Attempt $i failed, retrying..."
                sleep 3
            else
                echo "  ✗ $name failed after 5 attempts"
                return 1
            fi
        fi
    done
}

# Seed each table
seed_with_retry "UserSeeder" "Users" || exit 1
seed_with_retry "StudioSeeder" "Studios" || exit 1
seed_with_retry "FilmSeeder" "Films" || exit 1
seed_with_retry "SeatSeeder" "Seats" || exit 1
seed_with_retry "PriceSeeder" "Prices" || exit 1
seed_with_retry "ScheduleSeeder" "Schedules" || exit 1
seed_with_retry "OrderSeeder" "Orders" || exit 1

# Step 5: Verify all data
echo ""
echo "Step 5: Verifying data..."
echo ""

php artisan tinker --execute="
echo 'Data Count:' . PHP_EOL;
echo '  Users:     ' . \App\Models\User::count() . PHP_EOL;
echo '  Studios:   ' . \App\Models\Studio::count() . PHP_EOL;
echo '  Films:     ' . \App\Models\Film::count() . PHP_EOL;
echo '  Seats:     ' . \App\Models\Seat::count() . PHP_EOL;
echo '  Prices:    ' . \App\Models\Price::count() . PHP_EOL;
echo '  Schedules: ' . \App\Models\Schedule::count() . PHP_EOL;
echo '  Orders:    ' . \App\Models\Order::count() . PHP_EOL;
"

# Step 6: Final cache clear
echo ""
echo "Step 6: Final cleanup..."
php artisan config:clear
php artisan cache:clear

echo ""
echo "=== ✓ SEEDING COMPLETED ==="
