@echo off
setlocal enabledelayedexpansion

echo ==== Laravel Project Setup ====
echo.

:: Step 1: Composer install
echo [1/9] Running composer install...
cmd /c "composer install"
if errorlevel 1 goto failed
echo Composer install completed.

:: Step 2: NPM install
echo [2/9] Running npm install...
cmd /c "npm install"
if errorlevel 1 goto failed
echo NPM install completed.

:: Step 3: Copy env files
echo [3/9] Copying environment files...
copy /Y .env.example .env
if errorlevel 1 goto failed
copy /Y .env.testing.example .env.testing
if errorlevel 1 goto failed
echo Environment files copied.

:: Step 4: Generate app keys
echo [4/9] Generating app keys...
php artisan key:generate
if errorlevel 1 goto failed
php artisan key:generate --env=testing
if errorlevel 1 goto failed
echo App keys generated.

:: Step 5: Create SQLite files
echo [5/9] Creating database directory and files...
if not exist database mkdir database

if not exist database\database.sqlite (
    type nul > database\database.sqlite
    if errorlevel 1 goto failed
)

if not exist database\testDatabase.sqlite (
    type nul > database\testDatabase.sqlite
    if errorlevel 1 goto failed
)
echo SQLite files created.

:: Step 6: Migrate database
echo [6/9] Running migrations...
php artisan migrate:fresh
if errorlevel 1 goto failed
echo Migrations complete.

:: Step 7: Seed database
echo [7/9] Seeding database...
php artisan db:seed --class=InstallSeeder
if errorlevel 1 goto failed
echo Database seeded.

:: Step 8: Link storage
echo [8/9] Linking storage...
php artisan storage:link
if errorlevel 1 goto failed
echo Storage linked.

echo.
echo All steps completed successfully!
exit /b

:failed
echo.
echo ❌ Script failed during a step.
pause
exit /b 1
