@echo off
setlocal

echo Running composer install...
composer install

echo Running npm install...
npm install

echo Copying environment files...
copy /Y .env.example .env
copy /Y .env.testing.example .env.testing

echo Generating app key...
php artisan key:generate

echo Creating database file...
if not exist database mkdir database
type nul > database\database.sqlite
type nul > database\testingDatabase.sqlite

echo Running fresh migrations...
php artisan migrate:fresh

echo Seeding database...
php artisan db:seed --class=InstallSeeder

echo Linking storage...
php artisan storage:link

echo Setup complete!
pause
