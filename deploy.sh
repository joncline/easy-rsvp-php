#!/bin/bash

# Easy RSVP Laravel Deployment Script for DreamHost
# This script helps deploy the Laravel application to DreamHost shared hosting

echo "=== Easy RSVP Laravel Deployment Script ==="
echo "This script will help you deploy to DreamHost"
echo ""

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    echo "Error: composer.json not found. Please run this script from the Laravel project root."
    exit 1
fi

echo "Step 1: Installing/Updating Composer dependencies for production..."
composer install --optimize-autoloader --no-dev

echo ""
echo "Step 2: Clearing all Laravel caches first..."
# Clear all caches before regenerating to avoid stale service provider references
php artisan cache:clear 2>/dev/null || true
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Remove cached service provider files that may reference removed dev packages
rm -f bootstrap/cache/packages.php
rm -f bootstrap/cache/services.php

echo ""
echo "Step 3: Generating optimized autoloader..."
composer dump-autoload --optimize

echo ""
echo "Step 4: Caching configuration..."
php artisan config:cache

echo ""
echo "Step 5: Clearing and caching routes..."
php artisan route:clear
php artisan route:cache

echo ""
echo "Step 6: Clearing and caching views..."
php artisan view:clear
php artisan view:cache

echo ""
echo "Step 7: Building frontend assets..."
if [ -f "package.json" ]; then
    echo "WARNING: Node.js/npm may not be available on DreamHost shared hosting"
    echo "Please build assets locally before uploading to DreamHost:"
    echo "  npm ci"
    echo "  npm run build"
    echo "Then upload the built assets in public/build/ directory"
    
    # Try to build if npm is available locally
    if command -v npm >/dev/null 2>&1; then
        echo "npm found locally, attempting to build..."
        npm ci
        npm run build
    else
        echo "npm not found - you must build assets on a machine with Node.js installed"
    fi
else
    echo "No package.json found, skipping npm build"
fi

echo ""
echo "Step 8: Setting proper permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

echo ""
echo "=== Deployment Preparation Complete ==="
echo ""
echo "Next steps for DreamHost deployment:"
echo "1. Upload all files to your DreamHost domain folder"
echo "2. Copy .env.production to .env and update with your DreamHost details"
echo "3. Create a MySQL database in DreamHost panel"
echo "4. Update .env with your database credentials"
echo "5. Run: php artisan migrate --force"
echo "6. Run: php artisan key:generate"
echo "7. Set up your domain to point to the public folder"
echo ""
echo "Important: Make sure your DreamHost domain points to the 'public' directory!"
