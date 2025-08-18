# Troubleshooting Guide - Easy RSVP Laravel Deployment

## Issue 1: Laravel Pail Service Provider Error

### Problem:
```
Class "Laravel\Pail\PailServiceProvider" not found
```

### Cause:
When running `composer install --no-dev`, development packages like Laravel Pail are removed, but cached service provider files still reference them.

### Solution:
The updated deployment script now handles this by:
1. Clearing all caches before regenerating
2. Removing cached service provider files (`bootstrap/cache/packages.php` and `bootstrap/cache/services.php`)
3. Regenerating the autoloader and caches

### Manual Fix (if needed):
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Remove cached service provider files
rm -f bootstrap/cache/packages.php
rm -f bootstrap/cache/services.php

# Regenerate autoloader
composer dump-autoload --optimize

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Issue 2: Node.js Version Warning

### Problem:
```
npm WARN EBADENGINE Unsupported engine {
  package: 'laravel-vite-plugin@2.0.0',
  required: { node: '^20.19.0 || >=22.12.0' },
  current: { node: 'v18.19.1', npm: '9.2.0' }
}
```

### Cause:
Your current Node.js version (18.19.1) is older than what the Laravel Vite plugin requires (20.19.0+).

### Impact:
- **Good News**: The build still succeeded despite the warning
- **Assets Built**: Your CSS and JS files were compiled successfully
- **Deployment Ready**: You can proceed with deployment

### Solutions:

#### Option 1: Continue with Current Setup (Recommended for now)
- The build worked despite the warning
- Your assets are ready for deployment
- No immediate action needed

#### Option 2: Update Node.js (Optional)
```bash
# Using Node Version Manager (nvm)
nvm install 20
nvm use 20

# Or download from nodejs.org
# https://nodejs.org/en/download/
```

#### Option 3: Use DreamHost VPS (Future consideration)
- VPS hosting supports Node.js
- Can build assets directly on server
- Better for ongoing development

## Issue 3: DreamHost Shared Hosting Limitations

### Node.js Not Available on Shared Hosting
- **Limitation**: DreamHost shared hosting doesn't support Node.js
- **Solution**: Build assets locally, upload built files
- **Workflow**: 
  1. Run `npm run build` locally
  2. Upload `public/build/` directory to server
  3. Laravel serves pre-built assets

### Memory Limitations
- **Monitor**: Check memory usage in DreamHost panel
- **Optimize**: Use Laravel caching, optimize queries
- **Upgrade**: Consider VPS if memory limits are hit

## Deployment Status

### ✅ What's Working:
- Composer dependencies installed for production
- Frontend assets built successfully (despite Node.js warning)
- Laravel caches optimized
- File permissions set correctly
- All deployment files ready

### 📋 Next Steps:
1. **Upload to DreamHost**: All files are ready for upload
2. **Database Setup**: Create MySQL database in DreamHost panel
3. **Environment Config**: Copy `.env.production` to `.env` with your credentials
4. **Domain Setup**: Point document root to `public` directory
5. **Final Setup**: Run migrations and generate app key on server

## Quick Test Commands

### Test Local Build:
```bash
# Verify assets were built
ls -la public/build/

# Should show:
# - manifest.json
# - assets/app-*.css
# - assets/app-*.js
```

### Test Laravel Configuration:
```bash
# Check if Laravel is working
php artisan about

# Should show no errors and display app info
```

## Support Resources

### If You Encounter Issues:
1. **Check this troubleshooting guide first**
2. **Review deployment logs** for specific error messages
3. **Check DreamHost error logs** in your hosting panel
4. **Verify PHP version** compatibility (requires PHP 8.2+)
5. **Contact DreamHost support** for server-specific issues

### Common DreamHost Issues:
- **PHP Version**: Ensure PHP 8.2+ is selected in panel
- **Document Root**: Must point to `public` directory
- **File Permissions**: 755 for directories, 644 for files
- **Database Connection**: Verify credentials in `.env`

---

**Status**: Your application is ready for deployment to DreamHost shared hosting! 🚀
