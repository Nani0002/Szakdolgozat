#!/bin/bash

set -e  # Exit on any error

echo "Running composer install..."
composer install

echo "Running npm install..."
npm install

echo "Copying environment files..."
cp .env.example .env
cp .env.testing.example .env.testing

echo "Generating app key..."
php artisan key:generate

echo "Creating database file..."
mkdir -p database
touch database/database.sqlite

echo "Running fresh migrations..."
php artisan migrate:fresh

echo "Seeding database..."
php artisan db:seed --class=InstallSeeder

echo "Linking storage..."
php artisan storage:link

echo "Setup complete!"
