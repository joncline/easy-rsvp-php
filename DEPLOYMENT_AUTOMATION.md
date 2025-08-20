# Deployment Automation Guide

## Overview
This guide covers automated deployment of the Easy RSVP Laravel application to DreamHost shared hosting using VS Code deploy extension and SSH. It includes setup procedures, deployment workflows, and troubleshooting for seamless production deployments.

## Prerequisites

### Local Requirements
- **VS Code** with Deploy extension installed
- **SSH client** configured for DreamHost
- **Git** with repository access
- **Local development environment** set up (see [LOCAL_DEVELOPMENT.md](LOCAL_DEVELOPMENT.md))

### DreamHost Account Requirements
- **Shared hosting account** with SSH access enabled
- **MySQL database** created and configured
- **Domain/subdomain** configured and pointing to hosting
- **SSH key authentication** set up (recommended)

## VS Code Deploy Extension Setup

### 1. Install Deploy Extension
```bash
# Install via VS Code Extensions marketplace
# Search for "Deploy" by Marcel Joachim Kloubert
# Or install via command line
code --install-extension mkloubert.deploy
```

### 2. Configure Deploy Settings
Create `.vscode/settings.json` in project root:

```json
{
    "deploy": {
        "packages": [
            {
                "name": "Easy RSVP Production",
                "description": "Deploy to DreamHost production server",
                "files": [
                    "**/*",
                    "!node_modules/**",
                    "!.git/**",
                    "!.vscode/**",
                    "!storage/logs/**",
                    "!storage/framework/cache/**",
                    "!storage/framework/sessions/**",
                    "!storage/framework/views/**",
                    "!.env",
                    "!.env.local",
                    "!.env.example",
                    "!tests/**",
                    "!phpunit.xml",
                    "!*.log"
                ],
                "exclude": [
                    ".env",
                    ".env.local",
                    "storage/logs/*",
                    "storage/framework/cache/*",
                    "storage/framework/sessions/*",
                    "storage/framework/views/*",
                    "node_modules",
                    ".git",
                    "tests"
                ]
            }
        ],
        "targets": [
            {
                "type": "sftp",
                "name": "DreamHost Production",
                "description": "Production server on DreamHost",
                "host": "your-domain.com",
                "port": 22,
                "user": "your-username",
                "privateKey": "~/.ssh/id_rsa",
                "dir": "/home/your-username/your-domain.com",
                "mappings": {
                    "/": "/home/your-username/your-domain.com/"
                }
            }
        ]
    }
}
```

### 3. Create Deploy Configuration
Create `.vscode/deploy.json`:

```json
{
    "targets": [
        {
            "type": "sftp",
            "name": "production",
            "host": "your-domain.com",
            "port": 22,
            "user": "your-username",
            "privateKey": "~/.ssh/id_rsa",
            "dir": "/home/your-username/your-domain.com",
            "beforeDeploy": [
                {
                    "type": "command",
                    "command": "composer install --no-dev --optimize-autoloader",
                    "cwd": "${workspaceRoot}"
                }
            ],
            "afterDeploy": [
                {
                    "type": "ssh",
                    "command": "cd /home/your-username/your-domain.com && php artisan migrate --force"
                },
                {
                    "type": "ssh", 
                    "command": "cd /home/your-username/your-domain.com && php artisan config:cache"
                },
                {
                    "type": "ssh",
                    "command": "cd /home/your-username/your-domain.com && php artisan route:cache"
                }
            ]
        }
    ]
}
```

## SSH Configuration

### 1. Generate SSH Key Pair
```bash
# Generate new SSH key for DreamHost
ssh-keygen -t rsa -b 4096 -C "your-email@example.com" -f ~/.ssh/dreamhost_rsa

# Add to SSH agent
ssh-add ~/.ssh/dreamhost_rsa
```

### 2. Configure SSH Client
Create/edit `~/.ssh/config`:

```
Host dreamhost-production
    HostName your-domain.com
    User your-username
    Port 22
    IdentityFile ~/.ssh/dreamhost_rsa
    IdentitiesOnly yes
    ServerAliveInterval 60
    ServerAliveCountMax 3
```

### 3. Upload Public Key to DreamHost
```bash
# Copy public key to clipboard
cat ~/.ssh/dreamhost_rsa.pub

# SSH to DreamHost and add key
ssh your-username@your-domain.com
mkdir -p ~/.ssh
echo "your-public-key-content" >> ~/.ssh/authorized_keys
chmod 700 ~/.ssh
chmod 600 ~/.ssh/authorized_keys
exit
```

### 4. Test SSH Connection
```bash
# Test connection using config
ssh dreamhost-production

# Should connect without password prompt
# Test command execution
ssh dreamhost-production "php -v"
```

## DreamHost Server Setup

### 1. Directory Structure
```bash
# Connect to DreamHost
ssh dreamhost-production

# Create application directory structure
mkdir -p ~/your-domain.com
mkdir -p ~/your-domain.com/storage/logs
mkdir -p ~/your-domain.com/storage/framework/cache
mkdir -p ~/your-domain.com/storage/framework/sessions
mkdir -p ~/your-domain.com/storage/framework/views
mkdir -p ~/your-domain.com/bootstrap/cache

# Set permissions
chmod -R 755 ~/your-domain.com/storage
chmod -R 755 ~/your-domain.com/bootstrap/cache
```

### 2. Environment Configuration
```bash
# Create production environment file on server
ssh dreamhost-production
cd ~/your-domain.com

# Create .env.production template
cat > .env.production << 'EOF'
APP_NAME="Easy RSVP"
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=mysql.your-domain.com
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.dreamhost.com
MAIL_PORT=587
MAIL_USERNAME=your-email@your-domain.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

HASHIDS_SALT=your-production-salt-here
HASHIDS_LENGTH=8
EOF

# Copy to active environment
cp .env.production .env
```

### 3. Web Root Configuration
```bash
# DreamHost typically uses public_html as web root
# Create symlink or move public folder contents
ssh dreamhost-production

# Option 1: Symlink (if supported)
ln -sf ~/your-domain.com/public ~/public_html

# Option 2: Copy public contents to web root
cp -r ~/your-domain.com/public/* ~/public_html/
```

### 4. Composer Installation
```bash
# Install Composer on DreamHost (if not available)
ssh dreamhost-production
cd ~
curl -sS https://getcomposer.org/installer | php
mv composer.phar bin/composer
chmod +x bin/composer

# Add to PATH in .bashrc
echo 'export PATH="$HOME/bin:$PATH"' >> ~/.bashrc
source ~/.bashrc
```

## Deployment Workflows

### 1. Initial Deployment
```bash
# From VS Code Command Palette (Ctrl+Shift+P)
# Run: "Deploy: Deploy workspace"
# Select "DreamHost Production" target

# Or use terminal
cd /path/to/easy-rsvp-php
# Deploy using VS Code Deploy extension
```

### 2. Post-Deployment Setup (First Time)
```bash
# SSH to server for initial setup
ssh dreamhost-production
cd ~/your-domain.com

# Install dependencies
composer install --no-dev --optimize-autoloader

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### 3. Regular Deployment Process
```bash
# 1. Test locally first
cd /path/to/easy-rsvp-php
php artisan test
php artisan serve # Test functionality

# 2. Commit changes
git add .
git commit -m "Description of changes"
git push origin main

# 3. Deploy via VS Code
# Command Palette -> "Deploy: Deploy workspace"
# Select production target

# 4. Post-deployment commands (automated via deploy.json)
# - composer install --no-dev --optimize-autoloader
# - php artisan migrate --force
# - php artisan config:cache
# - php artisan route:cache
```

### 4. Rollback Procedure
```bash
# SSH to server
ssh dreamhost-production
cd ~/your-domain.com

# Backup current state
cp -r . ../backup-$(date +%Y%m%d-%H%M%S)

# Rollback to previous Git commit
git reset --hard HEAD~1

# Reinstall dependencies
composer install --no-dev --optimize-autoloader

# Run migrations if needed
php artisan migrate:rollback
# or
php artisan migrate:reset
php artisan migrate --force

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

## Automated Deployment Scripts

### 1. Pre-Deployment Script
Create `scripts/pre-deploy.sh`:

```bash
#!/bin/bash
set -e

echo "Starting pre-deployment checks..."

# Check if we're on main branch
BRANCH=$(git branch --show-current)
if [ "$BRANCH" != "main" ]; then
    echo "Error: Not on main branch. Current branch: $BRANCH"
    exit 1
fi

# Check for uncommitted changes
if ! git diff-index --quiet HEAD --; then
    echo "Error: Uncommitted changes detected"
    exit 1
fi

# Run tests
echo "Running tests..."
php artisan test

# Check Laravel installation
echo "Checking Laravel installation..."
php artisan --version

# Optimize for production
echo "Optimizing for production..."
composer install --no-dev --optimize-autoloader

echo "Pre-deployment checks completed successfully!"
```

### 2. Post-Deployment Script
Create `scripts/post-deploy.sh`:

```bash
#!/bin/bash
set -e

echo "Starting post-deployment tasks..."

# Set correct environment
cp .env.production .env

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Clear and cache configurations
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "Rebuilding caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
echo "Setting permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Verify deployment
echo "Verifying deployment..."
php artisan tinker --execute="echo 'Environment: ' . config('app.env');"

echo "Post-deployment tasks completed successfully!"
```

### 3. Health Check Script
Create `scripts/health-check.sh`:

```bash
#!/bin/bash

echo "Running health checks..."

# Check database connection
echo "Testing database connection..."
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database: OK';"

# Check environment
echo "Checking environment..."
ENV=$(php artisan tinker --execute="echo config('app.env');")
if [ "$ENV" != "production" ]; then
    echo "Warning: Environment is not production: $ENV"
fi

# Check critical files
echo "Checking critical files..."
if [ ! -f ".env" ]; then
    echo "Error: .env file missing"
    exit 1
fi

if [ ! -d "vendor" ]; then
    echo "Error: vendor directory missing"
    exit 1
fi

# Check permissions
echo "Checking permissions..."
if [ ! -w "storage/logs" ]; then
    echo "Warning: storage/logs not writable"
fi

echo "Health checks completed!"
```

## Monitoring and Logging

### 1. Deployment Logging
```bash
# Create deployment log directory
ssh dreamhost-production
mkdir -p ~/logs/deployment

# Log deployment activities
echo "$(date): Deployment started" >> ~/logs/deployment/deploy.log
```

### 2. Application Monitoring
```bash
# Monitor Laravel logs
ssh dreamhost-production
tail -f ~/your-domain.com/storage/logs/laravel.log

# Monitor web server logs (if accessible)
tail -f ~/logs/your-domain.com/http/access.log
tail -f ~/logs/your-domain.com/http/error.log
```

### 3. Automated Health Checks
Create cron job for regular health checks:

```bash
# Edit crontab
ssh dreamhost-production
crontab -e

# Add health check every 15 minutes
*/15 * * * * cd ~/your-domain.com && bash scripts/health-check.sh >> ~/logs/health-check.log 2>&1
```

## Troubleshooting

### Common Deployment Issues

#### SSH Connection Problems
```bash
# Test SSH connection
ssh -v dreamhost-production

# Check SSH key permissions
chmod 600 ~/.ssh/dreamhost_rsa
chmod 644 ~/.ssh/dreamhost_rsa.pub

# Verify SSH agent
ssh-add -l
```

#### Permission Errors
```bash
# Fix storage permissions on server
ssh dreamhost-production
cd ~/your-domain.com
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

#### Composer Issues
```bash
# Clear Composer cache on server
ssh dreamhost-production
cd ~/your-domain.com
composer clear-cache
composer install --no-dev --optimize-autoloader
```

#### Database Migration Errors
```bash
# Check database connection
ssh dreamhost-production
cd ~/your-domain.com
php artisan tinker --execute="DB::connection()->getPdo();"

# Run migrations manually
php artisan migrate --force --verbose
```

### VS Code Deploy Extension Issues

#### Extension Not Working
```bash
# Reload VS Code window
# Command Palette -> "Developer: Reload Window"

# Check deploy extension logs
# View -> Output -> Select "Deploy" from dropdown
```

#### Configuration Errors
```bash
# Validate JSON configuration
# Use JSON validator for .vscode/settings.json and .vscode/deploy.json

# Test SFTP connection manually
sftp your-username@your-domain.com
```

## Security Considerations

### 1. SSH Security
- Use SSH key authentication instead of passwords
- Regularly rotate SSH keys
- Limit SSH access to specific IP addresses if possible
- Use strong passphrases for SSH keys

### 2. File Permissions
```bash
# Secure file permissions on server
ssh dreamhost-production
cd ~/your-domain.com

# Secure .env file
chmod 600 .env

# Secure configuration files
chmod 644 config/*.php

# Secure storage directories
chmod -R 755 storage/
```

### 3. Environment Security
- Never commit .env files to version control
- Use different database credentials for production
- Regularly update application dependencies
- Monitor deployment logs for suspicious activity

## Backup Procedures

### 1. Pre-Deployment Backup
```bash
# Automated backup before deployment
ssh dreamhost-production
cd ~
tar -czf backup-$(date +%Y%m%d-%H%M%S).tar.gz your-domain.com/
```

### 2. Database Backup
```bash
# Backup database before migrations
ssh dreamhost-production
mysqldump -h mysql.your-domain.com -u your_db_user -p your_database > backup-$(date +%Y%m%d-%H%M%S).sql
```

---

**Next Steps**: After setting up deployment automation, proceed to [OPERATIONAL_RULES.md](OPERATIONAL_RULES.md) for comprehensive operational procedures.

**Related Documentation**:
- [LOCAL_DEVELOPMENT.md](LOCAL_DEVELOPMENT.md) - Local development setup
- [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) - Manual deployment procedures
- [SECURITY_NOTES.md](SECURITY_NOTES.md) - Security best practices
- [TROUBLESHOOTING.md](TROUBLESHOOTING.md) - General troubleshooting guide
