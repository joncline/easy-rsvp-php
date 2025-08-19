# Easy RSVP Deployment Guide

## Overview
This guide documents the deployment process for the Easy RSVP Laravel application to prevent environment configuration issues that have occurred previously.

## Production Environment Configuration

### Critical Environment Settings
The production `.env` file must contain these exact settings:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://rsvp.joncline.com/
LOG_LEVEL=error
```

**⚠️ CRITICAL:** Never deploy with `APP_ENV=local` as this exposes admin links publicly!

### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=mysql.joncline.com
DB_PORT=3306
DB_DATABASE=easy_rsvp
DB_USERNAME=easy_rsvp
DB_PASSWORD=lo*&7#ij^ew7JcR8
```

## Deployment Process

### 1. Pre-Deployment Checklist
- [ ] Verify `.env.production` template is up to date
- [ ] Test application locally with production-like settings
- [ ] Ensure all migrations are ready
- [ ] Backup current production database

### 2. Safe Deployment Steps

#### Step 1: Backup Current Environment
```bash
# On production server
cp .env .env.backup.$(date +%Y%m%d-%H%M%S)
```

#### Step 2: Deploy Code Changes
```bash
# Pull latest changes
git pull origin main

# Install/update dependencies
composer install --no-dev --optimize-autoloader
```

#### Step 3: Configure Environment
```bash
# Copy production environment template
cp .env.production .env

# Verify critical settings
grep -E "APP_ENV|APP_DEBUG|APP_URL|DB_HOST" .env
```

#### Step 4: Run Migrations and Cache
```bash
# Run database migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Rebuild optimized caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Post-Deployment Verification

#### Environment Verification
```bash
# Verify environment is production
php artisan tinker --execute="echo config('app.env');"

# Should output: production
```

#### Database Connection Test
```bash
# Test database connection
php artisan tinker --execute="DB::connection()->getPdo();"
```

#### Security Verification
- Visit a public event page (e.g., https://rsvp.joncline.com/l5-shabbat-dinner)
- Verify NO admin link is visible
- Admin access should only work via direct admin URL with token

## Common Issues and Solutions

### Issue 1: Admin Link Appearing Publicly
**Cause:** `APP_ENV=local` in production
**Solution:** 
```bash
# Fix environment setting
sed -i 's/APP_ENV=local/APP_ENV=production/' .env
php artisan config:clear
```

### Issue 2: Database Connection Errors
**Symptoms:** Connection refused, access denied
**Solutions:**
1. Verify database credentials in `.env`
2. Check DreamHost database hostname: `mysql.joncline.com`
3. Test direct connection: `mysql -h mysql.joncline.com -u easy_rsvp -p easy_rsvp`

### Issue 3: Environment File Reset
**Cause:** Deployment process overwriting `.env`
**Prevention:**
1. Always use `.env.production` as template
2. Never commit `.env` to git
3. Implement deployment script that preserves environment

## File Permissions

### Required Permissions
```bash
# Set proper permissions
chmod 644 .env
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### Security Considerations
- `.env` should not be web-accessible
- Verify `.htaccess` rules are in place
- Database passwords should be strong and unique

## Monitoring and Maintenance

### Log Monitoring
```bash
# Check application logs
tail -f storage/logs/laravel.log

# Check for database errors
grep -i "database\|connection" storage/logs/laravel.log
```

### Regular Maintenance
- Weekly: Check log files for errors
- Monthly: Verify environment configuration
- Quarterly: Update dependencies and security patches

## Emergency Recovery

### If Environment Gets Reset
1. **Immediate Action:**
   ```bash
   cp .env.production .env
   php artisan config:clear
   ```

2. **Verify Fix:**
   - Check admin link is hidden on public pages
   - Test database connectivity
   - Verify application functionality

### Rollback Procedure
```bash
# If deployment fails, rollback
git reset --hard HEAD~1
cp .env.backup.YYYYMMDD-HHMMSS .env
php artisan config:clear
```

## Automation Recommendations

### Deployment Script
Create `deploy.sh`:
```bash
#!/bin/bash
set -e

echo "Starting deployment..."

# Backup current state
cp .env .env.backup.$(date +%Y%m%d-%H%M%S)

# Pull changes
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Ensure production environment
cp .env.production .env

# Run migrations
php artisan migrate --force

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache

echo "Deployment complete!"
echo "Verifying environment..."
php artisan tinker --execute="echo 'Environment: ' . config('app.env');"
```

### Environment Validation
Add to deployment script:
```bash
# Validate critical settings
if grep -q "APP_ENV=local" .env; then
    echo "ERROR: APP_ENV is set to local in production!"
    exit 1
fi

if grep -q "APP_DEBUG=true" .env; then
    echo "ERROR: APP_DEBUG is enabled in production!"
    exit 1
fi
```

## Contact Information
- Repository: https://github.com/joncline/easy-rsvp-php.git
- Production URL: https://rsvp.joncline.com/
- Database Host: mysql.joncline.com

---
**Last Updated:** August 18, 2025
**Version:** 1.0
