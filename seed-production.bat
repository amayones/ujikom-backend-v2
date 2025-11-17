@echo off
echo === Production Seeding Script ===
echo Starting database seeding process...

echo Checking database connection...
php artisan db:show >nul 2>&1
if errorlevel 1 (
    echo ERROR: Database not ready
    exit /b 1
)
echo Database is ready!

echo Running migrations...
php artisan migrate:fresh --force
if errorlevel 1 (
    echo ERROR: Migration failed
    exit /b 1
)
echo Migration completed successfully

timeout /t 2 /nobreak >nul

echo Starting seeding process...

set SEEDERS=UserSeeder StudioSeeder FilmSeeder SeatSeeder PriceSeeder ScheduleSeeder OrderSeeder

for %%S in (%SEEDERS%) do (
    echo Running %%S...
    set /a RETRY=0
    :retry_%%S
    php artisan db:seed --class=%%S --force
    if errorlevel 1 (
        set /a RETRY+=1
        if !RETRY! LSS 3 (
            echo Warning: %%S failed, retrying... (!RETRY!/3^)
            timeout /t 3 /nobreak >nul
            goto retry_%%S
        ) else (
            echo ERROR: %%S failed after 3 attempts
            exit /b 1
        )
    )
    echo Success: %%S completed
    timeout /t 1 /nobreak >nul
)

echo.
echo === Seeding completed successfully ===
