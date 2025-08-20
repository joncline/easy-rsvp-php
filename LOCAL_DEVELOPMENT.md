# Local Development Setup Guide

## Overview
This guide covers setting up the Easy RSVP Laravel application for local development, including MySQL database configuration, environment setup, and testing procedures.

## Prerequisites

### Required Software
- **PHP 8.2+** with required extensions:
  - PDO MySQL
  - OpenSSL
  - Mbstring
  - Tokenizer
  - XML
  - Ctype
  - JSON
- **MySQL 8.0+** or **MariaDB 10.3+**
- **Composer** (latest version)
- **Git**
- **Node.js 18+** (for asset compilation if needed)

### Development Tools
- **VS Code** with recommended extensions:
  - PHP Intelephense
  - Laravel Extension Pack
  - MySQL (for database management)
  - Deploy (for deployment automation)

## MySQL Database Setup

### 1. Install MySQL
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install mysql-server

# macOS (using Homebrew)
brew install mysql

# Windows
# Download MySQL installer from https://dev.mysql.com/downloads/installer/
```

### 2. Start MySQL Service
```bash
# Ubuntu/Debian
sudo systemctl start mysql
sudo systemctl enable mysql

# macOS
brew services start mysql

# Windows
# Start MySQL service from Services panel or MySQL Workbench
```

### 3. Create Development Database
```bash
# Connect to MySQL as root
mysql -u root -p

# Create database and user
CREATE DATABASE easy_rsvp_local;
CREATE USER 'easy_rsvp_dev'@'localhost' IDENTIFIED BY 'dev_password_123';
GRANT ALL PRIVILEGES ON easy_rsvp_local.* TO 'easy_rsvp_dev'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. Test Database Connection
```bash
# Test connection with new user
mysql -u easy_rsvp_dev -p easy_rsvp_local
# Enter password: dev_password_123
# Should connect successfully
EXIT;
```

## Environment Configuration

### 1. Clone Repository
```bash
git clone https://github.com/joncline/easy-rsvp-php.git
cd easy-rsvp-php
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies (if needed for asset compilation)
npm install
```

### 3. Create Local Environment File
```bash
# Copy example environment file
cp .env.example .env.local
```

### 4. Configure Local Environment
Edit `.env.local` with the following settings:

```env
# Application Configuration
APP_NAME="Easy RSVP (Local)"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=easy_rsvp_local
DB_USERNAME=easy_rsvp_dev
DB_PASSWORD=dev_password_123

# Cache Configuration (for local development)
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

# Mail Configuration (for testing)
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Logging
LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# Hashids Configuration
HASHIDS_SALT=your_local_salt_here
HASHIDS_LENGTH=8
```

### 5. Generate Application Key
```bash
# Generate unique application key
php artisan key:generate --env=local

# This will update the APP_KEY in .env.local
```

### 6. Copy Local Environment to Active
```bash
# Use local environment as active .env
cp .env.local .env
```

## Database Migration and Seeding

### 1. Run Migrations
```bash
# Run all database migrations
php artisan migrate

# Check migration status
php artisan migrate:status
```

### 2. Seed Database (Optional)
```bash
# Run database seeders if available
php artisan db:seed

# Or run specific seeder
php artisan db:seed --class=DatabaseSeeder
```

### 3. Verify Database Setup
```bash
# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();
>>> exit
```

## Local Development Server

### 1. Start Development Server
```bash
# Start Laravel development server
php artisan serve

# Server will start at http://localhost:8000
```

### 2. Alternative Port
```bash
# Start on different port if 8000 is busy
php artisan serve --port=8080
```

### 3. Access Application
- **Main Application**: http://localhost:8000
- **Create Event**: http://localhost:8000 (root route)
- **Database**: Connect via MySQL client to `easy_rsvp_local`

## Testing Procedures

### 1. Basic Functionality Tests
```bash
# Test event creation
# 1. Visit http://localhost:8000
# 2. Fill out event form
# 3. Submit and verify event creation
# 4. Test RSVP functionality
# 5. Test admin panel access
```

### 2. Database Tests
```bash
# Run PHPUnit tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage (if configured)
php artisan test --coverage
```

### 3. Manual Testing Checklist
- [ ] Event creation form loads correctly
- [ ] Event can be created with all fields
- [ ] Event displays correctly on public page
- [ ] RSVP form works for guests
- [ ] Admin panel accessible with correct token
- [ ] Custom fields function properly
- [ ] Google Calendar integration works (if event has time)
- [ ] Admin URL recovery system works

## Environment Management

### Local vs Production Environments

**Local Environment (`.env.local`)**:
```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
DB_HOST=127.0.0.1
DB_DATABASE=easy_rsvp_local
MAIL_MAILER=log
LOG_LEVEL=debug
```

**Production Environment (`.env.production`)**:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://rsvp.joncline.com
DB_HOST=mysql.joncline.com
DB_DATABASE=easy_rsvp
MAIL_MAILER=smtp
LOG_LEVEL=error
```

### Switching Environments
```bash
# Switch to local development
cp .env.local .env
php artisan config:clear

# Switch to production (for testing production config locally)
cp .env.production .env
# Update database credentials for local testing
php artisan config:clear
```

## Common Development Tasks

### 1. Database Operations
```bash
# Fresh migration (drops all tables and re-migrates)
php artisan migrate:fresh

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Rollback last migration
php artisan migrate:rollback

# Reset database completely
php artisan migrate:reset
```

### 2. Cache Management
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild caches (for production testing)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Queue Management
```bash
# Process queue jobs (if using database queue)
php artisan queue:work

# Process specific queue
php artisan queue:work --queue=default
```

## Troubleshooting

### Common Issues

#### Database Connection Errors
```bash
# Check MySQL service status
sudo systemctl status mysql

# Check database credentials
mysql -u easy_rsvp_dev -p easy_rsvp_local

# Verify .env database settings
grep DB_ .env
```

#### Permission Errors
```bash
# Fix storage permissions
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Fix ownership (if needed)
sudo chown -R $USER:www-data storage/
sudo chown -R $USER:www-data bootstrap/cache/
```

#### Composer Issues
```bash
# Update Composer
composer self-update

# Clear Composer cache
composer clear-cache

# Reinstall dependencies
rm -rf vendor/
composer install
```

#### Laravel Artisan Issues
```bash
# Clear all Laravel caches
php artisan optimize:clear

# Regenerate autoload files
composer dump-autoload

# Check Laravel installation
php artisan --version
```

### Debug Mode

#### Enable Debug Mode
```env
# In .env file
APP_DEBUG=true
LOG_LEVEL=debug
```

#### View Logs
```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# View specific log level
grep ERROR storage/logs/laravel.log
```

## Development Workflow

### 1. Daily Development Routine
```bash
# Start development session
git pull origin main
composer install
php artisan migrate
php artisan serve

# End development session
git add .
git commit -m "Description of changes"
git push origin feature-branch
```

### 2. Feature Development
```bash
# Create feature branch
git checkout -b feature/new-feature

# Make changes and test locally
php artisan test
php artisan serve

# Commit and push
git add .
git commit -m "Add new feature"
git push origin feature/new-feature
```

### 3. Database Changes
```bash
# Create new migration
php artisan make:migration create_new_table

# Edit migration file
# Run migration
php artisan migrate

# Test migration rollback
php artisan migrate:rollback
php artisan migrate
```

## Performance Optimization

### Local Development Optimization
```bash
# Use file-based caching for faster development
# In .env:
CACHE_STORE=file
SESSION_DRIVER=file

# Disable unnecessary services
QUEUE_CONNECTION=sync
MAIL_MAILER=log
```

### Database Optimization
```bash
# Add database indexes (in migrations)
# Optimize MySQL configuration for development
# Use database query logging for debugging
```

## Security Considerations

### Local Security
- Use strong passwords for local MySQL users
- Keep `.env` files out of version control
- Use HTTPS for local development when testing production features
- Regularly update dependencies

### Environment Isolation
- Never use production credentials in local environment
- Use separate API keys for local testing
- Test security features in isolated local environment

---

**Next Steps**: After completing local setup, proceed to [DEPLOYMENT_AUTOMATION.md](DEPLOYMENT_AUTOMATION.md) for production deployment configuration.

**Related Documentation**:
- [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) - Production deployment procedures
- [TROUBLESHOOTING.md](TROUBLESHOOTING.md) - General troubleshooting guide
- [SECURITY_NOTES.md](SECURITY_NOTES.md) - Security best practices
