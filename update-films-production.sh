#!/bin/bash
# Force Update Films in Production

echo "=== Updating Films in Production ==="

# Truncate films table and reseed
php artisan tinker --execute="
    DB::table('order_items')->delete();
    DB::table('orders')->delete();
    DB::table('schedules')->delete();
    DB::table('films')->delete();
    
    echo 'Deleted old films and related data' . PHP_EOL;
"

sleep 2

# Reseed films
php artisan db:seed --class=FilmSeeder --force

echo ""
echo "Verifying films..."
php artisan tinker --execute="
    \$films = \App\Models\Film::all();
    foreach (\$films as \$film) {
        echo \$film->title . ' - ' . \$film->poster . PHP_EOL;
    }
    echo PHP_EOL . 'Total films: ' . \$films->count() . PHP_EOL;
"

# Reseed schedules
echo ""
echo "Reseeding schedules..."
php artisan db:seed --class=ScheduleSeeder --force

echo ""
echo "=== Films Updated Successfully ==="
