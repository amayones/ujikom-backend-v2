@echo off
REM Script untuk fix film data di production (Windows)
REM Usage: fix-films.bat [production-url]

if "%1"=="" (
    echo Error: URL production tidak diberikan
    echo Usage: fix-films.bat https://your-domain.com
    exit /b 1
)

set PRODUCTION_URL=%1

echo Fixing films data di production...
echo URL: %PRODUCTION_URL%
echo.

curl -X GET "%PRODUCTION_URL%/api/fix-films-production"

echo.
echo Done! Silakan cek frontend untuk melihat poster dan trailer
