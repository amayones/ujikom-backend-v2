#!/bin/bash
# Force Seeding Script - Guaranteed to work

set -e

echo "=========================================="
echo "  FORCE SEEDING SCRIPT - PRODUCTION"
echo "=========================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Function to run command with retry
run_with_retry() {
    local cmd="$1"
    local max_attempts=5
    local attempt=1
    
    while [ $attempt -le $max_attempts ]; do
        echo -e "${YELLOW}Attempt $attempt/$max_attempts${NC}"
        
        if eval "$cmd"; then
            echo -e "${GREEN}✓ Success${NC}"
            return 0
        else
            if [ $attempt -lt $max_attempts ]; then
                echo -e "${RED}✗ Failed, retrying in 5 seconds...${NC}"
                sleep 5
                attempt=$((attempt + 1))
            else
                echo -e "${RED}✗ Failed after $max_attempts attempts${NC}"
                return 1
            fi
        fi
    done
}

# Check database connection
echo "1. Checking database connection..."
run_with_retry "php artisan db:show" || {
    echo -e "${RED}ERROR: Cannot connect to database${NC}"
    exit 1
}
echo ""

# Fresh migration
echo "2. Running fresh migrations..."
run_with_retry "php artisan migrate:fresh --force" || {
    echo -e "${RED}ERROR: Migration failed${NC}"
    exit 1
}
echo ""

sleep 3

# Seed each table
echo "3. Seeding database..."
echo ""

SEEDERS=(
    "UserSeeder:Users"
    "StudioSeeder:Studios"
    "FilmSeeder:Films"
    "SeatSeeder:Seats"
    "PriceSeeder:Prices"
    "ScheduleSeeder:Schedules"
    "OrderSeeder:Orders"
)

for SEEDER_INFO in "${SEEDERS[@]}"; do
    IFS=':' read -r SEEDER TABLE <<< "$SEEDER_INFO"
    echo "→ Seeding $TABLE..."
    
    run_with_retry "php artisan db:seed --class=$SEEDER --force" || {
        echo -e "${RED}ERROR: $SEEDER failed${NC}"
        exit 1
    }
    
    sleep 2
    echo ""
done

# Verify data
echo "4. Verifying seeded data..."
echo ""

php artisan tinker --execute="
    \$counts = [
        'Users' => \App\Models\User::count(),
        'Studios' => \App\Models\Studio::count(),
        'Films' => \App\Models\Film::count(),
        'Seats' => \App\Models\Seat::count(),
        'Prices' => \App\Models\Price::count(),
        'Schedules' => \App\Models\Schedule::count(),
        'Orders' => \App\Models\Order::count(),
    ];
    
    foreach (\$counts as \$model => \$count) {
        echo str_pad(\$model . ':', 15) . \$count . PHP_EOL;
    }
    
    \$total = array_sum(\$counts);
    echo str_repeat('-', 20) . PHP_EOL;
    echo 'Total records: ' . \$total . PHP_EOL;
    
    if (\$total == 0) {
        echo PHP_EOL . 'WARNING: No data seeded!' . PHP_EOL;
        exit(1);
    }
"

if [ $? -ne 0 ]; then
    echo -e "${RED}ERROR: No data found after seeding${NC}"
    exit 1
fi

echo ""

# Clear caches
echo "5. Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✓ Caches cleared${NC}"
echo ""

echo "=========================================="
echo -e "${GREEN}  ✓ SEEDING COMPLETED SUCCESSFULLY${NC}"
echo "=========================================="
