#!/bin/bash

# Production Database Connection Fix Script
# Run this on your production server to fix MySQL hostname issues

echo "=== Production Database Connection Fix ==="
echo "This script fixes common MySQL connection issues on production"
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan not found. Please run this script from the Laravel project root."
    exit 1
fi

echo "Step 1: Checking .env file..."
if [ ! -f ".env" ]; then
    if [ -f ".env.production" ]; then
        echo "📋 Copying .env.production to .env..."
        cp .env.production .env
        echo "✅ .env file created from .env.production"
    else
        echo "❌ Error: Neither .env nor .env.production found!"
        exit 1
    fi
else
    echo "✅ .env file exists"
fi

echo ""
echo "Step 2: Aggressively clearing ALL Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo ""
echo "Step 3: Removing ALL cached files..."
rm -f bootstrap/cache/packages.php
rm -f bootstrap/cache/services.php
rm -f bootstrap/cache/config.php
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/views/*
rm -rf storage/framework/sessions/*

echo ""
echo "Step 4: Force regenerating configuration cache with correct values..."
php artisan config:cache

echo ""
echo "Step 4: Checking database configuration..."
echo "Current DB_HOST setting:"
grep "DB_HOST=" .env || echo "DB_HOST not found in .env"

echo ""
echo "Step 5: Testing database connection..."
php debug-db-config.php

echo ""
echo "=== Fix Complete ==="
echo ""
echo "If you're still getting connection errors:"
echo "1. Verify your DreamHost MySQL hostname in the panel"
echo "2. Check that mysql.joncline.com is the correct hostname"
echo "3. Ensure your database user has proper permissions"
echo "4. Try connecting via command line: mysql -h mysql.joncline.com -u easy_rsvp -p"
echo ""
echo "Common DreamHost MySQL hostnames:"
echo "- mysql.yourdomain.com"
echo "- mysql.dreamhost.com"
echo "- Check your DreamHost panel for the exact hostname"
