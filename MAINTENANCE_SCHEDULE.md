# Maintenance Schedule and Procedures

## Overview
This document defines the regular maintenance schedule and procedures for the Easy RSVP Laravel application. It ensures optimal performance, security, and reliability through systematic maintenance activities.

## Maintenance Philosophy

### Proactive Maintenance
- **PRINCIPLE**: Prevent issues before they occur through regular maintenance
- **PRINCIPLE**: Maintain system health through continuous monitoring and optimization
- **PRINCIPLE**: Minimize downtime through planned maintenance windows
- **PRINCIPLE**: Document all maintenance activities for audit and improvement

### Maintenance Windows
- **PRIMARY WINDOW**: Sundays 2:00 AM - 6:00 AM EST (low traffic period)
- **EMERGENCY WINDOW**: Any time for critical security or stability issues
- **NOTIFICATION**: All planned maintenance communicated 48 hours in advance
- **DURATION**: Standard maintenance limited to 2 hours maximum

## Daily Maintenance Tasks

### Automated Daily Checks (Cron Jobs)

#### System Health Monitoring
```bash
# Schedule: Every day at 6:00 AM EST
0 6 * * * /home/username/scripts/daily-health-check.sh

#!/bin/bash
# Daily Health Check Script
LOG_FILE="/home/username/logs/daily-health-$(date +%Y%m%d).log"
echo "=== Daily Health Check - $(date) ===" >> $LOG_FILE

# 1. Database Connection Test
echo "Testing database connection..." >> $LOG_FILE
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database: OK';" >> $LOG_FILE 2>&1

# 2. Application Response Test
echo "Testing application response..." >> $LOG_FILE
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" https://your-domain.com)
if [ "$RESPONSE" = "200" ]; then
    echo "Application: OK (HTTP $RESPONSE)" >> $LOG_FILE
else
    echo "ERROR: Application returned HTTP $RESPONSE" >> $LOG_FILE
    # Send alert email
    echo "Application health check failed" | mail -s "ALERT: Easy RSVP Health Check Failed" admin@your-domain.com
fi

# 3. Disk Space Check
echo "Checking disk space..." >> $LOG_FILE
DISK_USAGE=$(df / | tail -1 | awk '{print $5}' | sed 's/%//')
echo "Disk usage: ${DISK_USAGE}%" >> $LOG_FILE
if [ $DISK_USAGE -gt 80 ]; then
    echo "WARNING: High disk usage detected" >> $LOG_FILE
    echo "Disk usage at ${DISK_USAGE}%" | mail -s "WARNING: High Disk Usage" admin@your-domain.com
fi

# 4. Log File Size Check
echo "Checking log file sizes..." >> $LOG_FILE
LOG_SIZE=$(du -m storage/logs/laravel.log 2>/dev/null | cut -f1 || echo "0")
echo "Laravel log size: ${LOG_SIZE}MB" >> $LOG_FILE
if [ $LOG_SIZE -gt 100 ]; then
    echo "WARNING: Large log file detected" >> $LOG_FILE
fi

# 5. Error Log Review
echo "Checking for recent errors..." >> $LOG_FILE
ERROR_COUNT=$(grep -c "ERROR" storage/logs/laravel.log 2>/dev/null || echo "0")
echo "Error count in logs: $ERROR_COUNT" >> $LOG_FILE
if [ $ERROR_COUNT -gt 10 ]; then
    echo "WARNING: High error count in logs" >> $LOG_FILE
    tail -20 storage/logs/laravel.log | grep ERROR >> $LOG_FILE
fi

echo "=== Daily Health Check Complete ===" >> $LOG_FILE
```

#### Backup Verification
```bash
# Schedule: Every day at 7:00 AM EST
0 7 * * * /home/username/scripts/verify-backups.sh

#!/bin/bash
# Backup Verification Script
BACKUP_DIR="/home/username/backups"
LOG_FILE="/home/username/logs/backup-verification-$(date +%Y%m%d).log"

echo "=== Backup Verification - $(date) ===" >> $LOG_FILE

# Check if yesterday's backup exists
YESTERDAY=$(date -d "yesterday" +%Y%m%d)
DB_BACKUP="$BACKUP_DIR/db-backup-$YESTERDAY*.sql"
APP_BACKUP="$BACKUP_DIR/app-backup-$YESTERDAY*.tar.gz"

if ls $DB_BACKUP 1> /dev/null 2>&1; then
    echo "Database backup found for $YESTERDAY" >> $LOG_FILE
    # Verify backup integrity
    if gzip -t $DB_BACKUP 2>/dev/null; then
        echo "Database backup integrity: OK" >> $LOG_FILE
    else
        echo "ERROR: Database backup integrity check failed" >> $LOG_FILE
    fi
else
    echo "ERROR: No database backup found for $YESTERDAY" >> $LOG_FILE
    echo "Missing database backup for $YESTERDAY" | mail -s "ERROR: Missing Database Backup" admin@your-domain.com
fi

if ls $APP_BACKUP 1> /dev/null 2>&1; then
    echo "Application backup found for $YESTERDAY" >> $LOG_FILE
    # Verify backup integrity
    if tar -tzf $APP_BACKUP >/dev/null 2>&1; then
        echo "Application backup integrity: OK" >> $LOG_FILE
    else
        echo "ERROR: Application backup integrity check failed" >> $LOG_FILE
    fi
else
    echo "ERROR: No application backup found for $YESTERDAY" >> $LOG_FILE
fi

echo "=== Backup Verification Complete ===" >> $LOG_FILE
```

### Manual Daily Tasks (5 minutes)

#### Morning Checklist (9:00 AM EST)
```bash
# Daily Manual Checklist
1. Review overnight health check logs
   tail -50 /home/username/logs/daily-health-$(date +%Y%m%d).log

2. Check application accessibility
   curl -I https://your-domain.com

3. Review error notifications
   Check email for any automated alerts

4. Verify backup completion
   ls -la /home/username/backups/ | tail -5

5. Quick performance check
   # Test page load time
   curl -w "@curl-format.txt" -o /dev/null -s https://your-domain.com
```

## Weekly Maintenance Tasks

### Sunday Maintenance Window (2:00 AM - 4:00 AM EST)

#### Week 1: Security and Updates
```bash
# First Sunday of each month
#!/bin/bash
# Weekly Security Maintenance Script

echo "=== Weekly Security Maintenance - $(date) ==="

# 1. Security Updates Check
echo "Checking for security updates..."
composer audit

# 2. Dependency Updates (non-breaking)
echo "Updating dependencies..."
composer update --no-dev --optimize-autoloader

# 3. Laravel Framework Updates (patch versions only)
echo "Checking Laravel updates..."
php artisan --version

# 4. Security Configuration Review
echo "Reviewing security configuration..."
# Check .env file permissions
ls -la .env
# Verify APP_DEBUG is false
grep APP_DEBUG .env

# 5. SSL Certificate Check
echo "Checking SSL certificate..."
openssl s_client -connect your-domain.com:443 -servername your-domain.com < /dev/null 2>/dev/null | openssl x509 -noout -dates

# 6. File Permission Audit
echo "Auditing file permissions..."
find . -type f -perm 777 -ls
find storage/ -type d ! -perm 755 -ls

echo "=== Security Maintenance Complete ==="
```

#### Week 2: Performance Optimization
```bash
# Second Sunday of each month
#!/bin/bash
# Weekly Performance Maintenance Script

echo "=== Weekly Performance Maintenance - $(date) ==="

# 1. Clear and Rebuild Caches
echo "Optimizing caches..."
php artisan optimize:clear
php artisan optimize

# 2. Database Optimization
echo "Optimizing database..."
php artisan db:show --counts
# Run database maintenance queries if needed

# 3. Log File Cleanup
echo "Cleaning up log files..."
find storage/logs/ -name "*.log" -mtime +7 -delete
find /home/username/logs/ -name "*.log" -mtime +30 -delete

# 4. Temporary File Cleanup
echo "Cleaning temporary files..."
find storage/framework/cache/ -type f -mtime +1 -delete
find storage/framework/sessions/ -type f -mtime +1 -delete
find storage/framework/views/ -type f -mtime +7 -delete

# 5. Performance Metrics Collection
echo "Collecting performance metrics..."
# Database query performance
php artisan tinker --execute="
\$start = microtime(true);
DB::table('events')->count();
\$time = microtime(true) - \$start;
echo 'Query time: ' . round(\$time * 1000, 2) . 'ms';
"

# 6. Disk Space Analysis
echo "Analyzing disk space usage..."
du -sh storage/
du -sh vendor/
du -sh /home/username/backups/

echo "=== Performance Maintenance Complete ==="
```

#### Week 3: Backup and Recovery Testing
```bash
# Third Sunday of each month
#!/bin/bash
# Weekly Backup Testing Script

echo "=== Weekly Backup Testing - $(date) ==="

# 1. Create Test Backup
echo "Creating test backup..."
TIMESTAMP=$(date +%Y%m%d-%H%M%S)
mysqldump -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE > test-backup-$TIMESTAMP.sql

# 2. Verify Backup Integrity
echo "Verifying backup integrity..."
if [ -s "test-backup-$TIMESTAMP.sql" ]; then
    echo "Backup file created successfully"
    # Check if backup contains expected tables
    TABLES=$(grep -c "CREATE TABLE" test-backup-$TIMESTAMP.sql)
    echo "Tables in backup: $TABLES"
    if [ $TABLES -lt 5 ]; then
        echo "WARNING: Backup may be incomplete"
    fi
else
    echo "ERROR: Backup file is empty or missing"
fi

# 3. Test Database Restoration (on test database)
echo "Testing database restoration..."
# Create test database
mysql -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD -e "CREATE DATABASE IF NOT EXISTS easy_rsvp_test;"
# Restore backup to test database
mysql -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD easy_rsvp_test < test-backup-$TIMESTAMP.sql
# Verify restoration
TEST_COUNT=$(mysql -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD easy_rsvp_test -e "SELECT COUNT(*) FROM events;" 2>/dev/null | tail -1)
echo "Test database event count: $TEST_COUNT"

# 4. Cleanup Test Files
echo "Cleaning up test files..."
rm test-backup-$TIMESTAMP.sql
mysql -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD -e "DROP DATABASE IF EXISTS easy_rsvp_test;"

# 5. Backup Retention Management
echo "Managing backup retention..."
find /home/username/backups/ -name "*.sql" -mtime +30 -delete
find /home/username/backups/ -name "*.tar.gz" -mtime +30 -delete

echo "=== Backup Testing Complete ==="
```

#### Week 4: System Health and Monitoring
```bash
# Fourth Sunday of each month
#!/bin/bash
# Weekly System Health Check Script

echo "=== Weekly System Health Check - $(date) ==="

# 1. Comprehensive Health Check
echo "Running comprehensive health check..."
php artisan health:check 2>/dev/null || echo "Health check command not available"

# 2. Resource Usage Analysis
echo "Analyzing resource usage..."
echo "Memory usage:"
free -h
echo "CPU usage:"
top -bn1 | grep "Cpu(s)"
echo "Disk I/O:"
iostat -x 1 1 2>/dev/null || echo "iostat not available"

# 3. Network Connectivity Test
echo "Testing network connectivity..."
ping -c 4 8.8.8.8
nslookup your-domain.com

# 4. Service Status Check
echo "Checking service status..."
systemctl status mysql 2>/dev/null || echo "MySQL status check not available"
systemctl status apache2 2>/dev/null || systemctl status nginx 2>/dev/null || echo "Web server status check not available"

# 5. Log Analysis
echo "Analyzing logs for patterns..."
echo "Most common errors in last week:"
grep ERROR storage/logs/laravel.log | tail -1000 | awk '{print $4}' | sort | uniq -c | sort -nr | head -10

# 6. Security Scan
echo "Running security scan..."
# Check for suspicious files
find . -name "*.php" -mtime -7 -exec grep -l "eval\|base64_decode\|shell_exec" {} \;
# Check for unusual file permissions
find . -type f -perm 777 -ls

echo "=== System Health Check Complete ==="
```

## Monthly Maintenance Tasks

### First Sunday of Each Month (Extended Maintenance Window)

#### Comprehensive System Maintenance
```bash
#!/bin/bash
# Monthly Comprehensive Maintenance Script

echo "=== Monthly Comprehensive Maintenance - $(date) ==="

# 1. Full System Backup
echo "Creating full system backup..."
BACKUP_DATE=$(date +%Y%m%d)
tar -czf /home/username/backups/full-system-backup-$BACKUP_DATE.tar.gz \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='node_modules' \
    --exclude='.git' \
    /home/username/your-domain.com/

# 2. Database Maintenance
echo "Performing database maintenance..."
# Optimize all tables
mysql -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE -e "
OPTIMIZE TABLE events, rsvps, custom_fields, custom_field_responses;
ANALYZE TABLE events, rsvps, custom_fields, custom_field_responses;
"

# 3. Security Audit
echo "Performing security audit..."
# Check for outdated packages
composer outdated
# Review file permissions
find . -type f -perm 777 -ls > /tmp/world_writable_files.txt
if [ -s /tmp/world_writable_files.txt ]; then
    echo "WARNING: World-writable files found"
    cat /tmp/world_writable_files.txt
fi

# 4. Performance Analysis
echo "Analyzing performance trends..."
# Collect performance metrics
echo "Database size:"
mysql -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD -e "
SELECT 
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.tables 
WHERE table_schema = '$DB_DATABASE'
ORDER BY (data_length + index_length) DESC;
"

# 5. Capacity Planning
echo "Reviewing capacity metrics..."
echo "Disk usage trend:"
df -h /
echo "Database growth:"
mysql -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE -e "SELECT COUNT(*) as total_events FROM events;"
mysql -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE -e "SELECT COUNT(*) as total_rsvps FROM rsvps;"

# 6. Update Documentation
echo "Updating maintenance documentation..."
echo "Last comprehensive maintenance: $(date)" >> /home/username/logs/maintenance-history.log

echo "=== Monthly Comprehensive Maintenance Complete ==="
```

## Quarterly Maintenance Tasks

### Quarterly Review and Planning (First Sunday of Quarter)

#### System Review and Optimization
```bash
#!/bin/bash
# Quarterly System Review Script

echo "=== Quarterly System Review - $(date) ==="

# 1. Performance Review
echo "Reviewing quarterly performance..."
# Analyze 3 months of logs for trends
find storage/logs/ -name "*.log" -mtime -90 -exec grep -h "ERROR\|WARNING" {} \; | \
    awk '{print $1}' | sort | uniq -c | sort -nr > quarterly-issues.txt

# 2. Security Review
echo "Conducting quarterly security review..."
# Check for security updates
composer audit --format=json > security-audit.json
# Review access logs
grep "admin" /var/log/apache2/access.log | tail -100 > admin-access-review.txt

# 3. Capacity Planning
echo "Updating capacity planning..."
# Database growth analysis
mysql -h $DB_HOST -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE -e "
SELECT 
    MONTH(created_at) as month,
    YEAR(created_at) as year,
    COUNT(*) as events_created
FROM events 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH)
GROUP BY YEAR(created_at), MONTH(created_at)
ORDER BY year, month;
"

# 4. Backup Strategy Review
echo "Reviewing backup strategy..."
# Test backup restoration
# Document backup sizes and retention
ls -lah /home/username/backups/ | tail -20

# 5. Documentation Update
echo "Updating system documentation..."
# Generate system inventory
php artisan --version > system-inventory.txt
composer show >> system-inventory.txt
mysql --version >> system-inventory.txt

echo "=== Quarterly System Review Complete ==="
```

## Annual Maintenance Tasks

### Annual System Overhaul (Scheduled Downtime Required)

#### Major Updates and Migrations
```bash
#!/bin/bash
# Annual System Overhaul Script

echo "=== Annual System Overhaul - $(date) ==="

# 1. Major Framework Updates
echo "Planning major framework updates..."
# Review Laravel upgrade path
# Plan PHP version upgrades
# Review dependency updates

# 2. Security Hardening Review
echo "Conducting annual security hardening..."
# Review all security configurations
# Update SSL certificates
# Review access controls

# 3. Performance Optimization
echo "Implementing annual performance optimizations..."
# Database schema optimization
# Index analysis and optimization
# Query performance review

# 4. Disaster Recovery Testing
echo "Testing disaster recovery procedures..."
# Full system restoration test
# Backup integrity verification
# Recovery time measurement

# 5. Documentation Overhaul
echo "Updating all system documentation..."
# Review and update all procedures
# Update contact information
# Review maintenance schedules

echo "=== Annual System Overhaul Complete ==="
```

## Maintenance Tracking and Reporting

### Maintenance Log Format
```bash
# Maintenance Activity Log Entry Format
Date: YYYY-MM-DD HH:MM:SS
Type: [Daily|Weekly|Monthly|Quarterly|Annual|Emergency]
Duration: HH:MM
Performed By: [Name/System]
Activities:
- Activity 1: Status
- Activity 2: Status
- Activity 3: Status
Issues Found:
- Issue 1: Description and resolution
- Issue 2: Description and resolution
Next Actions:
- Action 1: Due date
- Action 2: Due date
```

### Monthly Maintenance Report Template
```markdown
# Monthly Maintenance Report - [Month Year]

## Executive Summary
- System uptime: XX.X%
- Critical issues: X
- Performance metrics: [Summary]
- Security status: [Status]

## Maintenance Activities Completed
- Daily checks: XX/XX completed
- Weekly maintenance: X/X completed
- Monthly tasks: [List]

## Issues and Resolutions
- [Issue 1]: [Resolution]
- [Issue 2]: [Resolution]

## Performance Metrics
- Average response time: XXXms
- Database query performance: XXXms
- Disk usage: XX%
- Memory usage: XX%

## Recommendations
- [Recommendation 1]
- [Recommendation 2]

## Next Month's Focus
- [Priority 1]
- [Priority 2]
```

## Emergency Maintenance Procedures

### Unscheduled Maintenance Triggers
- **Critical Security Vulnerability**: Immediate patching required
- **Performance Degradation**: Response time > 10 seconds
- **Database Issues**: Connection failures or corruption
- **Disk Space Critical**: > 95% usage
- **Memory Issues**: Consistent high memory usage

### Emergency Maintenance Process
```bash
# Emergency Maintenance Checklist
1. Assess severity and impact
2. Notify stakeholders (if user-facing impact)
3. Create emergency backup
4. Implement fix with minimal downtime
5. Verify fix effectiveness
6. Document emergency maintenance
7. Schedule follow-up review
```

## Maintenance Tools and Scripts

### Required Tools Installation
```bash
# Install maintenance tools
sudo apt update
sudo apt install -y htop iotop mysql-client curl wget

# Install monitoring tools
pip install glances  # System monitoring
npm install -g pm2   # Process monitoring (if using Node.js)
```

### Custom Maintenance Scripts Location
```
/home/username/scripts/
├── daily-health-check.sh
├── weekly-security-update.sh
├── monthly-comprehensive.sh
├── backup-verification.sh
├── performance-check.sh
└── emergency-rollback.sh
```

---

**Maintenance Schedule Adherence**: All maintenance activities must be completed according to this schedule. Deviations must be documented with justification.

**Continuous Improvement**: This maintenance schedule should be reviewed and updated quarterly based on system performance and lessons learned.

**Related Documentation**:
- [OPERATIONAL_RULES.md](OPERATIONAL_RULES.md) - Operational procedures and rules
- [EMERGENCY_PROCEDURES.md](EMERGENCY_PROCEDURES.md) - Emergency response procedures
- [DEPLOYMENT_AUTOMATION.md](DEPLOYMENT_AUTOMATION.md) - Automated deployment procedures
- [LOCAL_DEVELOPMENT.md](LOCAL_DEVELOPMENT.md) - Local development setup
