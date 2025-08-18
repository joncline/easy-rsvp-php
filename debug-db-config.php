<?php
/**
 * Database Configuration Debug Script
 * Run this on your production server to diagnose database connection issues
 */

echo "=== Database Configuration Debug ===\n\n";

// Check if we're in a Laravel environment
if (file_exists('artisan')) {
    echo "✅ Laravel application detected\n\n";
    
    // Load Laravel environment
    require_once 'vendor/autoload.php';
    
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    echo "📋 Current Database Configuration:\n";
    echo "DB_CONNECTION: " . env('DB_CONNECTION', 'NOT SET') . "\n";
    echo "DB_HOST: " . env('DB_HOST', 'NOT SET') . "\n";
    echo "DB_PORT: " . env('DB_PORT', 'NOT SET') . "\n";
    echo "DB_DATABASE: " . env('DB_DATABASE', 'NOT SET') . "\n";
    echo "DB_USERNAME: " . env('DB_USERNAME', 'NOT SET') . "\n";
    echo "DB_PASSWORD: " . (env('DB_PASSWORD') ? '[SET]' : 'NOT SET') . "\n\n";
    
    echo "🔧 Laravel Config Cache Status:\n";
    if (file_exists('bootstrap/cache/config.php')) {
        echo "❌ Config is cached - this might be using old values\n";
        echo "Run: php artisan config:clear\n\n";
    } else {
        echo "✅ Config is not cached\n\n";
    }
    
    echo "📁 Environment File Check:\n";
    if (file_exists('.env')) {
        echo "✅ .env file exists\n";
        $envContent = file_get_contents('.env');
        if (strpos($envContent, 'mysql.joncline.com') !== false) {
            echo "✅ .env contains mysql.joncline.com hostname\n";
        } else {
            echo "❌ .env does not contain mysql.joncline.com hostname\n";
            echo "Check if .env.production was copied to .env\n";
        }
    } else {
        echo "❌ .env file does not exist\n";
        echo "Copy .env.production to .env\n";
    }
    
    echo "\n🔌 Testing Database Connection:\n";
    try {
        $pdo = new PDO(
            'mysql:host=' . env('DB_HOST') . ';port=' . env('DB_PORT') . ';dbname=' . env('DB_DATABASE'),
            env('DB_USERNAME'),
            env('DB_PASSWORD'),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        echo "✅ Direct PDO connection successful!\n";
        $pdo = null;
    } catch (Exception $e) {
        echo "❌ Direct PDO connection failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n🚀 Recommended Actions:\n";
    echo "1. Run: php artisan config:clear\n";
    echo "2. Run: php artisan cache:clear\n";
    echo "3. Ensure .env.production is copied to .env\n";
    echo "4. Verify MySQL hostname: mysql.joncline.com\n";
    echo "5. Check DreamHost MySQL settings in panel\n";
    
} else {
    echo "❌ Not in a Laravel application directory\n";
}

echo "\n=== Debug Complete ===\n";
?>
