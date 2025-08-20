# Operational Rules and Procedures

## Overview
This document establishes comprehensive operational rules and procedures for the Easy RSVP Laravel application. These rules ensure consistent, secure, and reliable deployment and maintenance operations across all environments.

## Core Operational Principles

### 1. Environment Separation
- **RULE**: Never use production credentials in local development
- **RULE**: Always maintain separate `.env` files for local and production
- **RULE**: Production environment must have `APP_ENV=production` and `APP_DEBUG=false`
- **ENFORCEMENT**: Automated checks in deployment scripts verify environment settings

### 2. Code Quality Standards
- **RULE**: All code changes must pass local tests before deployment
- **RULE**: No direct production server code modifications allowed
- **RULE**: All changes must go through version control (Git)
- **ENFORCEMENT**: Pre-deployment scripts check for uncommitted changes and test failures

### 3. Database Management
- **RULE**: All database changes must use Laravel migrations
- **RULE**: Never manually modify production database structure
- **RULE**: Always backup database before running migrations in production
- **ENFORCEMENT**: Automated backup procedures before deployment

## Deployment Rules

### Pre-Deployment Requirements

#### Code Quality Checks
```bash
# MANDATORY: Run before every deployment
php artisan test                    # All tests must pass
composer install --no-dev          # Dependencies must install cleanly
php artisan config:clear           # Configuration must be valid
```

#### Branch Management
- **RULE**: Only deploy from `main` branch
- **RULE**: Feature branches must be merged via pull request
- **RULE**: No direct commits to `main` branch in production
- **VERIFICATION**: 
  ```bash
  # Check current branch
  BRANCH=$(git branch --show-current)
  if [ "$BRANCH" != "main" ]; then
      echo "ERROR: Must deploy from main branch"
      exit 1
  fi
  ```

#### Environment Validation
- **RULE**: Production `.env` must be validated before deployment
- **RULE**: Database credentials must be tested before deployment
- **RULE**: All required environment variables must be present
- **VERIFICATION**:
  ```bash
  # Required environment variables
  REQUIRED_VARS=("APP_KEY" "DB_HOST" "DB_DATABASE" "DB_USERNAME" "DB_PASSWORD")
  for var in "${REQUIRED_VARS[@]}"; do
      if [ -z "${!var}" ]; then
          echo "ERROR: Required environment variable $var is missing"
          exit 1
      fi
  done
  ```

### Deployment Process Rules

#### Step 1: Pre-Deployment Validation
```bash
# MANDATORY CHECKS (must all pass)
1. git status --porcelain | wc -l == 0     # No uncommitted changes
2. git branch --show-current == "main"     # On main branch
3. php artisan test --stop-on-failure      # All tests pass
4. composer validate                       # Composer.json valid
5. php artisan config:clear               # Config loads without errors
```

#### Step 2: Backup Procedures
```bash
# MANDATORY BACKUPS (before any changes)
1. Database backup:
   mysqldump -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE > backup-$(date +%Y%m%d-%H%M%S).sql

2. Application backup:
   tar -czf app-backup-$(date +%Y%m%d-%H%M%S).tar.gz /path/to/application

3. Environment backup:
   cp .env .env.backup.$(date +%Y%m%d-%H%M%S)
```

#### Step 3: Deployment Execution
```bash
# MANDATORY SEQUENCE (must be followed exactly)
1. Deploy code files (excluding .env, storage, vendor)
2. composer install --no-dev --optimize-autoloader
3. cp .env.production .env
4. php artisan migrate --force
5. php artisan config:cache
6. php artisan route:cache
7. php artisan view:cache
8. chmod -R 755 storage/ bootstrap/cache/
```

#### Step 4: Post-Deployment Verification
```bash
# MANDATORY VERIFICATION (must all succeed)
1. php artisan tinker --execute="echo config('app.env');" == "production"
2. php artisan tinker --execute="DB::connection()->getPdo();"  # Database connection
3. curl -I https://your-domain.com | grep "200 OK"            # Site accessible
4. Check admin links are hidden on public pages
```

### Rollback Rules

#### Automatic Rollback Triggers
- **RULE**: Rollback immediately if site returns 500 errors
- **RULE**: Rollback if database connection fails after deployment
- **RULE**: Rollback if admin links appear on public pages
- **RULE**: Rollback if any post-deployment verification fails

#### Rollback Procedure
```bash
# EMERGENCY ROLLBACK SEQUENCE
1. ssh production-server
2. cd /path/to/application
3. cp .env.backup.LATEST .env
4. git reset --hard HEAD~1
5. composer install --no-dev --optimize-autoloader
6. php artisan migrate:rollback (if migrations were run)
7. php artisan config:clear && php artisan config:cache
8. Verify site functionality
```

## Environment Management Rules

### Local Development Environment

#### Setup Requirements
- **RULE**: Use separate MySQL database for local development
- **RULE**: Local environment must use `.env.local` template
- **RULE**: Never use production database credentials locally
- **RULE**: Local `APP_ENV` must be set to `local`

#### Local Environment Configuration
```env
# MANDATORY LOCAL SETTINGS
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
DB_HOST=127.0.0.1
DB_DATABASE=easy_rsvp_local
MAIL_MAILER=log
LOG_LEVEL=debug
```

### Production Environment

#### Security Requirements
- **RULE**: `APP_DEBUG` must be `false` in production
- **RULE**: `APP_ENV` must be `production`
- **RULE**: `LOG_LEVEL` must be `error` or `warning`
- **RULE**: Database passwords must be strong (minimum 12 characters)

#### Production Environment Configuration
```env
# MANDATORY PRODUCTION SETTINGS
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
LOG_LEVEL=error
CACHE_STORE=file
SESSION_DRIVER=file
```

## Security Rules

### Access Control
- **RULE**: SSH access to production server requires key-based authentication
- **RULE**: Database access limited to application and authorized administrators
- **RULE**: Admin URLs must never be exposed on public pages in production
- **RULE**: All sensitive configuration in `.env` files, never in code

### File Permissions
```bash
# MANDATORY PRODUCTION PERMISSIONS
.env                    600 (read/write owner only)
config/*.php           644 (read-only for web server)
storage/               755 (writable by application)
bootstrap/cache/       755 (writable by application)
public/                644 (readable by web server)
```

### Data Protection
- **RULE**: Security answers must be encrypted in database
- **RULE**: Admin tokens must be UUIDs, never predictable
- **RULE**: Database backups must be stored securely
- **RULE**: Log files must not contain sensitive information

## Monitoring Rules

### Application Monitoring

#### Health Check Requirements
- **RULE**: Database connectivity must be verified every 15 minutes
- **RULE**: Application response time must be monitored
- **RULE**: Error logs must be reviewed daily
- **RULE**: Disk space must be monitored (alert at 80% full)

#### Health Check Script
```bash
#!/bin/bash
# MANDATORY HEALTH CHECKS

# Database connection
php artisan tinker --execute="DB::connection()->getPdo();" || exit 1

# Environment verification
ENV=$(php artisan tinker --execute="echo config('app.env');")
if [ "$ENV" != "production" ]; then
    echo "CRITICAL: Environment is not production: $ENV"
    exit 1
fi

# Disk space check
DISK_USAGE=$(df / | tail -1 | awk '{print $5}' | sed 's/%//')
if [ $DISK_USAGE -gt 80 ]; then
    echo "WARNING: Disk usage at ${DISK_USAGE}%"
fi

# Log file size check
LOG_SIZE=$(du -m storage/logs/laravel.log | cut -f1)
if [ $LOG_SIZE -gt 100 ]; then
    echo "WARNING: Log file size is ${LOG_SIZE}MB"
fi
```

### Performance Monitoring
- **RULE**: Page load times must be under 3 seconds
- **RULE**: Database query times must be logged if over 1 second
- **RULE**: Memory usage must not exceed 80% of available
- **RULE**: Cache hit rates must be monitored and optimized

## Maintenance Rules

### Regular Maintenance Tasks

#### Daily Tasks
```bash
# MANDATORY DAILY CHECKS
1. Review error logs: tail -100 storage/logs/laravel.log | grep ERROR
2. Check disk space: df -h
3. Verify backup completion: ls -la backups/ | tail -5
4. Monitor application response: curl -I https://your-domain.com
```

#### Weekly Tasks
```bash
# MANDATORY WEEKLY MAINTENANCE
1. Update dependencies: composer update (in staging first)
2. Clear old log files: find storage/logs/ -name "*.log" -mtime +30 -delete
3. Database optimization: php artisan optimize:clear && php artisan optimize
4. Security scan: composer audit
```

#### Monthly Tasks
```bash
# MANDATORY MONTHLY MAINTENANCE
1. Full database backup and verification
2. Security updates: composer update --with-all-dependencies
3. Performance review: analyze slow query logs
4. Capacity planning: review resource usage trends
```

### Backup Rules

#### Backup Requirements
- **RULE**: Database must be backed up before every deployment
- **RULE**: Full application backup must be created weekly
- **RULE**: Backups must be tested monthly for restoration
- **RULE**: Backups must be stored in multiple locations

#### Backup Procedures
```bash
# AUTOMATED BACKUP SCRIPT
#!/bin/bash
DATE=$(date +%Y%m%d-%H%M%S)

# Database backup
mysqldump -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE > db-backup-$DATE.sql

# Application backup
tar -czf app-backup-$DATE.tar.gz --exclude='storage/logs/*' --exclude='storage/framework/cache/*' /path/to/app

# Verify backups
if [ -f "db-backup-$DATE.sql" ] && [ -f "app-backup-$DATE.tar.gz" ]; then
    echo "Backup completed successfully: $DATE"
else
    echo "ERROR: Backup failed: $DATE"
    exit 1
fi

# Clean old backups (keep 30 days)
find . -name "*backup*" -mtime +30 -delete
```

## Incident Response Rules

### Severity Levels

#### Critical (P1) - Immediate Response Required
- Site completely down (500 errors, database connection failure)
- Security breach or data exposure
- Admin links visible on public pages
- **RESPONSE TIME**: 15 minutes
- **ESCALATION**: Immediate notification to all stakeholders

#### High (P2) - Response Within 1 Hour
- Performance degradation (page load > 10 seconds)
- Partial functionality failure
- Email delivery issues
- **RESPONSE TIME**: 1 hour
- **ESCALATION**: Notification to technical team

#### Medium (P3) - Response Within 4 Hours
- Minor UI issues
- Non-critical feature failures
- Log file warnings
- **RESPONSE TIME**: 4 hours
- **ESCALATION**: Standard ticket process

### Incident Response Procedures

#### Critical Incident Response
```bash
# IMMEDIATE ACTIONS (within 15 minutes)
1. Assess impact and confirm severity
2. Implement immediate mitigation (rollback if necessary)
3. Notify stakeholders
4. Begin detailed investigation
5. Document all actions taken

# ROLLBACK DECISION CRITERIA
- Site returning 500 errors: IMMEDIATE ROLLBACK
- Database connection failure: IMMEDIATE ROLLBACK  
- Security issue detected: IMMEDIATE ROLLBACK
- Admin links visible publicly: IMMEDIATE ROLLBACK
```

#### Post-Incident Procedures
```bash
# MANDATORY POST-INCIDENT ACTIONS
1. Root cause analysis within 24 hours
2. Update procedures to prevent recurrence
3. Test prevention measures
4. Update monitoring to detect similar issues
5. Document lessons learned
```

## Change Management Rules

### Change Approval Process
- **RULE**: All production changes require approval
- **RULE**: Emergency changes can be implemented immediately but must be documented within 24 hours
- **RULE**: Changes must be tested in staging environment first
- **RULE**: All changes must have rollback plan

### Change Documentation
```markdown
# MANDATORY CHANGE RECORD FORMAT
## Change ID: YYYY-MM-DD-###
## Description: [Brief description of change]
## Justification: [Why change is needed]
## Risk Assessment: [Low/Medium/High]
## Testing Performed: [List of tests]
## Rollback Plan: [How to undo if needed]
## Approval: [Approver name and date]
## Implementation: [Date/time implemented]
## Verification: [Post-change verification results]
```

## Compliance and Audit Rules

### Documentation Requirements
- **RULE**: All operational procedures must be documented
- **RULE**: All changes must be logged with timestamps
- **RULE**: Access logs must be retained for 90 days
- **RULE**: Backup logs must be retained for 1 year

### Audit Trail
```bash
# MANDATORY LOGGING
1. All SSH access: logged in /var/log/auth.log
2. All database changes: logged via Laravel migrations
3. All deployments: logged with timestamps and user
4. All configuration changes: logged in version control
```

## Performance Standards

### Response Time Requirements
- **STANDARD**: Page load time < 3 seconds (95th percentile)
- **STANDARD**: Database query time < 1 second (average)
- **STANDARD**: API response time < 500ms (average)
- **STANDARD**: Time to first byte < 1 second

### Availability Requirements
- **STANDARD**: 99.9% uptime (maximum 8.76 hours downtime per year)
- **STANDARD**: Planned maintenance windows < 4 hours per month
- **STANDARD**: Recovery time objective (RTO) < 1 hour
- **STANDARD**: Recovery point objective (RPO) < 24 hours

## Violation Handling

### Rule Violation Consequences
- **Minor Violations**: Documentation and training
- **Major Violations**: Process review and additional controls
- **Critical Violations**: Immediate access review and incident response

### Violation Reporting
```bash
# VIOLATION REPORT FORMAT
Date: [YYYY-MM-DD]
Rule Violated: [Specific rule reference]
Impact: [Description of impact]
Root Cause: [Why violation occurred]
Corrective Action: [What was done to fix]
Prevention: [How to prevent recurrence]
```

---

**Enforcement**: These rules are enforced through automated scripts, monitoring systems, and regular audits. Violations must be reported and addressed according to the incident response procedures.

**Review Schedule**: This document must be reviewed quarterly and updated as needed to reflect operational changes and lessons learned.

**Related Documentation**:
- [LOCAL_DEVELOPMENT.md](LOCAL_DEVELOPMENT.md) - Local development procedures
- [DEPLOYMENT_AUTOMATION.md](DEPLOYMENT_AUTOMATION.md) - Automated deployment setup
- [MAINTENANCE_SCHEDULE.md](MAINTENANCE_SCHEDULE.md) - Scheduled maintenance procedures
- [EMERGENCY_PROCEDURES.md](EMERGENCY_PROCEDURES.md) - Emergency response procedures
