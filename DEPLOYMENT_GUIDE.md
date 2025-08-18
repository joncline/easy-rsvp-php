# Easy RSVP Laravel - DreamHost Deployment Guide

This guide will walk you through deploying the Easy RSVP Laravel application to DreamHost shared hosting.

## Prerequisites

- DreamHost shared hosting account
- Domain configured in DreamHost panel
- SSH access enabled (optional but recommended)
- MySQL database created in DreamHost panel

## Step 1: Prepare Your Local Environment

1. Navigate to the Laravel project directory:
   ```bash
   cd easy-rsvp-laravel
   ```

2. Run the deployment preparation script:
   ```bash
   ./deploy.sh
   ```

   This script will:
   - Install production dependencies
   - Optimize autoloader
   - Cache configuration, routes, and views
   - Build frontend assets
   - Set proper permissions

## Step 2: Create MySQL Database in DreamHost

1. Log into your DreamHost panel
2. Go to "Goodies" → "MySQL Databases"
3. Create a new database:
   - Database Name: `easy_rsvp_production` (or your preferred name)
   - Use in: Select your domain
   - Create a database user with full privileges

4. Note down:
   - Database name
   - Database username
   - Database password
   - Database hostname (usually `mysql.yourdomain.com`)

## Step 3: Configure Environment Variables

1. Copy the production environment file:
   ```bash
   cp .env.production .env
   ```

2. Edit the `.env` file with your DreamHost details:
   ```env
   APP_NAME="Easy RSVP"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com

   # Update with your DreamHost database details
   DB_CONNECTION=mysql
   DB_HOST=mysql.yourdomain.com
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password

   # Update with your domain email settings
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.dreamhost.com
   MAIL_PORT=587
   MAIL_USERNAME=your_email@yourdomain.com
   MAIL_PASSWORD=your_email_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="noreply@yourdomain.com"

   # Generate a new secret salt for production
   HASHIDS_SALT=your-unique-production-salt-here
   ```

## Step 4: Upload Files to DreamHost

### Option A: Using FTP/SFTP
1. Connect to your DreamHost server via FTP/SFTP
2. Upload all files to your domain's directory (usually `/home/username/yourdomain.com/`)
3. Make sure to upload the `.env` file you configured

### Option B: Using SSH (Recommended)
1. SSH into your DreamHost server
2. Navigate to your domain directory
3. Clone or upload your project files
4. Ensure the `.env` file is properly configured

## Step 5: Set Document Root

**IMPORTANT**: Your domain must point to the `public` directory of your Laravel application.

1. In DreamHost panel, go to "Domains" → "Manage Domains"
2. Click "Edit" next to your domain
3. Change the "Web directory" to point to the `public` folder:
   - If your files are in `/home/username/yourdomain.com/`
   - Set web directory to: `/home/username/yourdomain.com/public`
4. Save changes

## Step 6: Run Database Migrations

1. SSH into your DreamHost server
2. Navigate to your Laravel project directory
3. Run the migrations:
   ```bash
   php artisan migrate --force
   ```

4. Generate a new application key:
   ```bash
   php artisan key:generate
   ```

## Step 7: Set File Permissions

Ensure proper permissions are set:
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 644 .env
```

## Step 8: Test Your Deployment

1. Visit your domain in a web browser
2. You should see the Easy RSVP application
3. Test creating an event to ensure database connectivity
4. Test RSVP functionality

## Troubleshooting

### Common Issues:

1. **500 Internal Server Error**
   - Check that document root points to `public` directory
   - Verify file permissions (755 for directories, 644 for files)
   - Check error logs in DreamHost panel

2. **Database Connection Error**
   - Verify database credentials in `.env`
   - Ensure database user has proper privileges
   - Check database hostname

3. **Missing Dependencies**
   - Run `composer install --no-dev` on the server
   - Ensure PHP version compatibility (requires PHP 8.2+)

4. **Email Not Working**
   - Verify SMTP settings in `.env`
   - Check that email account exists in DreamHost
   - Test with a simple mail client first

### Useful Commands:

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check application status
php artisan about
```

## Security Considerations

1. Ensure `.env` file is not publicly accessible
2. Keep your Laravel application updated
3. Use strong database passwords
4. Enable HTTPS for your domain
5. Regularly backup your database

## Maintenance

- Regularly update dependencies: `composer update`
- Monitor error logs in DreamHost panel
- Keep Laravel framework updated
- Backup database regularly

## Support

If you encounter issues:
1. Check DreamHost error logs
2. Review Laravel logs in `storage/logs/`
3. Consult DreamHost documentation
4. Check Laravel documentation

---

**Note**: This deployment guide is specifically tailored for DreamHost shared hosting. Some steps may vary depending on your specific hosting configuration.
