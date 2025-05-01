#!/bin/bash

set -e

echo "==== Laravel Project Setup ===="
echo

# Step 1: Composer install
echo "[1/9] Running composer install..."
composer install
echo "Composer install completed."

# Step 2: NPM install
echo "[2/9] Running npm install..."
npm install
echo "NPM install completed."

# Step 3: Copy env files
echo "[3/9] Copying environment files..."
cp .env.example .env
cp .env.testing.example .env.testing
echo "Environment files copied."

# Step 4: Generate app keys
echo "[4/9] Generating app keys..."
php artisan key:generate
php artisan key:generate --env=testing
echo "App keys generated."

# Step 5: Create SQLite files
echo "[5/9] Creating database directory and files..."
mkdir -p database
[ -f database/database.sqlite ] || touch database/database.sqlite
[ -f database/testDatabase.sqlite ] || touch database/testDatabase.sqlite
echo "SQLite files created."

# Step 6: Migrate database
echo "[6/9] Running migrations..."
php artisan migrate:fresh
echo "Migrations complete."

# Step 7: Seed database
echo "[7/9] Seeding database..."
php artisan db:seed --class=InstallSeeder
echo "Database seeded."

# Step 8: Link storage
echo "[8/9] Linking storage..."
php artisan storage:link
echo "Storage linked."

echo
echo "✅ All steps completed successfully!"
