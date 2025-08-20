# Cline AI Development Workflows

## Overview
This document provides project-specific Cline workflows for the Easy RSVP Laravel application. It covers AI-assisted development, testing, and deployment procedures that integrate with the existing local development and deployment automation systems.

## Cline Development Philosophy

### AI-Assisted Development Principles
- **PRINCIPLE**: Use Cline to accelerate development while maintaining code quality
- **PRINCIPLE**: Leverage AI for repetitive tasks, testing, and documentation
- **PRINCIPLE**: Maintain human oversight for critical decisions and architecture
- **PRINCIPLE**: Document AI-assisted workflows for team consistency

### Integration with Existing Systems
- **LOCAL DEVELOPMENT**: Seamlessly integrate with MySQL local setup
- **DEPLOYMENT**: Work with VS Code deploy extension and SSH automation
- **TESTING**: Enhance existing Laravel testing framework
- **MAINTENANCE**: Support operational rules and maintenance schedules

## Core Cline Workflows

### 1. Project Setup and Initialization

#### New Feature Development Workflow
```markdown
# Cline Prompt Template: New Feature Development

I'm working on the Easy RSVP Laravel application. I need to develop a new feature: [FEATURE_DESCRIPTION]

**Project Context:**
- Laravel 12.0 application with MySQL database
- Uses Hashids for URL obfuscation
- Models: Event, RSVP, CustomField, CustomFieldResponse
- Deployment to DreamHost shared hosting
- Local development with separate .env configuration

**Current Task:**
[DETAILED_FEATURE_REQUIREMENTS]

**Please help me:**
1. Analyze the existing codebase structure
2. Create necessary migrations, models, controllers
3. Update routes and views as needed
4. Write appropriate tests
5. Update documentation

**Files to consider:**
- app/Models/Event.php (main event model)
- app/Http/Controllers/EventController.php
- routes/web.php
- resources/views/events/
- database/migrations/

Please start by reading the relevant existing files to understand the current implementation.
```

#### Database Migration Workflow
```markdown
# Cline Prompt Template: Database Changes

I need to make database changes to the Easy RSVP application.

**Change Required:**
[DESCRIBE_DATABASE_CHANGES]

**Project Context:**
- Laravel 12.0 with MySQL
- Existing tables: events, rsvps, custom_fields, custom_field_responses
- Uses Laravel migrations for all database changes
- Must maintain backward compatibility

**Please help me:**
1. Create appropriate Laravel migration files
2. Update relevant Eloquent models
3. Add necessary relationships
4. Create rollback procedures
5. Update any affected controllers/views

**Requirements:**
- Follow Laravel migration best practices
- Include proper indexes for performance
- Maintain referential integrity
- Test migration rollback capability

Please start by examining the existing migration files in database/migrations/
```

### 2. Local Development Workflows

#### Local Environment Setup with Cline
```markdown
# Cline Prompt Template: Local Development Setup

Help me set up the Easy RSVP Laravel application for local development.

**System Information:**
- OS: [YOUR_OS]
- PHP Version: [PHP_VERSION]
- MySQL Version: [MYSQL_VERSION]

**Requirements:**
- Set up local MySQL database
- Configure .env.local file
- Install dependencies
- Run migrations
- Start development server

**Reference Documentation:**
Please read LOCAL_DEVELOPMENT.md for the complete setup procedures and follow those guidelines.

**Tasks:**
1. Verify system requirements
2. Create local database and user
3. Configure environment files
4. Install Composer dependencies
5. Run database migrations
6. Test local server startup

Please execute each step and verify it works before proceeding to the next.
```

#### Local Testing Workflow
```markdown
# Cline Prompt Template: Local Testing

I need to test changes to the Easy RSVP application locally.

**Changes Made:**
[DESCRIBE_CHANGES]

**Testing Requirements:**
1. Run PHPUnit tests
2. Test functionality manually
3. Verify database changes
4. Check for regressions
5. Test RSVP workflow end-to-end

**Test Scenarios:**
- Event creation with all fields
- RSVP submission (Yes/No/Maybe)
- Admin panel access and functionality
- Custom fields functionality
- Google Calendar integration (if applicable)

**Environment:**
- Local development server (http://localhost:8000)
- Local MySQL database (easy_rsvp_local)
- Debug mode enabled

Please help me execute comprehensive testing and report any issues found.
```

### 3. Deployment Workflows

#### Pre-Deployment Validation with Cline
```markdown
# Cline Prompt Template: Pre-Deployment Validation

I'm preparing to deploy changes to the Easy RSVP production server.

**Changes to Deploy:**
[DESCRIBE_CHANGES]

**Pre-Deployment Checklist:**
Please help me verify all items from OPERATIONAL_RULES.md:

1. Code Quality Checks:
   - Run `php artisan test`
   - Run `composer install --no-dev`
   - Verify `php artisan config:clear`

2. Branch Management:
   - Confirm on main branch
   - No uncommitted changes
   - All changes pushed to repository

3. Environment Validation:
   - Production .env file ready
   - Database credentials tested
   - Required environment variables present

**Reference Files:**
- OPERATIONAL_RULES.md (deployment rules)
- DEPLOYMENT_AUTOMATION.md (deployment procedures)

Please execute each validation step and confirm readiness for deployment.
```

#### Deployment Execution with Cline
```markdown
# Cline Prompt Template: Deployment Execution

Execute deployment of Easy RSVP application to DreamHost production server.

**Deployment Method:**
- VS Code Deploy extension
- SSH to DreamHost shared hosting
- Automated post-deployment scripts

**Deployment Steps:**
Follow the procedures in DEPLOYMENT_AUTOMATION.md:

1. Pre-deployment backup
2. Deploy code files via VS Code extension
3. SSH to server for post-deployment tasks:
   - composer install --no-dev --optimize-autoloader
   - cp .env.production .env
   - php artisan migrate --force
   - php artisan config:cache
   - php artisan route:cache
   - chmod -R 755 storage/ bootstrap/cache/

4. Post-deployment verification:
   - Test site accessibility
   - Verify environment is production
   - Check admin links are hidden
   - Test core functionality

Please execute deployment and verify each step completes successfully.
```

### 4. Troubleshooting Workflows

#### Error Investigation with Cline
```markdown
# Cline Prompt Template: Error Investigation

I'm experiencing an issue with the Easy RSVP application.

**Error Description:**
[DESCRIBE_ERROR]

**Environment:**
- [Local/Production]
- Error occurred: [TIMESTAMP]
- User actions that triggered error: [ACTIONS]

**Investigation Steps:**
1. Check Laravel logs: storage/logs/laravel.log
2. Check web server logs (if accessible)
3. Verify database connectivity
4. Check file permissions
5. Review recent changes

**Reference Documentation:**
- TROUBLESHOOTING.md
- OPERATIONAL_RULES.md

**Please help me:**
1. Analyze the error logs
2. Identify root cause
3. Propose solution
4. Test fix in appropriate environment
5. Document resolution

Start by examining the relevant log files and system status.
```

#### Performance Issue Analysis
```markdown
# Cline Prompt Template: Performance Analysis

The Easy RSVP application is experiencing performance issues.

**Performance Symptoms:**
[DESCRIBE_PERFORMANCE_ISSUES]

**Analysis Required:**
1. Database query performance
2. Page load times
3. Memory usage
4. Cache effectiveness

**Tools Available:**
- Laravel Debugbar (local)
- Database query logs
- Server resource monitoring
- Application profiling

**Investigation Steps:**
1. Enable query logging
2. Analyze slow queries
3. Check cache hit rates
4. Review resource usage
5. Identify bottlenecks

**Reference:**
- MAINTENANCE_SCHEDULE.md (performance monitoring)
- OPERATIONAL_RULES.md (performance standards)

Please help me systematically analyze and resolve performance issues.
```

### 5. Code Review and Quality Assurance

#### Code Review with Cline
```markdown
# Cline Prompt Template: Code Review

Please review the following code changes for the Easy RSVP application.

**Changes Made:**
[DESCRIBE_CHANGES OR PROVIDE FILE PATHS]

**Review Criteria:**
1. Laravel best practices compliance
2. Security considerations
3. Performance implications
4. Code maintainability
5. Testing coverage

**Project Standards:**
- Follow existing code patterns
- Maintain backward compatibility
- Ensure proper error handling
- Include appropriate validation
- Follow PSR coding standards

**Specific Areas to Review:**
- Database queries and relationships
- Input validation and sanitization
- Error handling and logging
- Security (especially admin access)
- Performance impact

Please provide detailed feedback and suggestions for improvement.
```

#### Security Review Workflow
```markdown
# Cline Prompt Template: Security Review

Conduct a security review of changes to the Easy RSVP application.

**Changes to Review:**
[DESCRIBE_CHANGES]

**Security Checklist:**
1. Input validation and sanitization
2. SQL injection prevention
3. XSS protection
4. CSRF token usage
5. Authentication and authorization
6. File upload security (if applicable)
7. Environment variable usage
8. Admin access controls

**Critical Security Areas:**
- Admin token handling
- Database queries
- User input processing
- File permissions
- Environment configuration

**Reference:**
- SECURITY_NOTES.md
- OPERATIONAL_RULES.md (security rules)

Please identify any security vulnerabilities and recommend fixes.
```

### 6. Documentation and Maintenance

#### Documentation Update Workflow
```markdown
# Cline Prompt Template: Documentation Update

Update documentation for the Easy RSVP application.

**Changes Requiring Documentation:**
[DESCRIBE_CHANGES]

**Documentation Files to Update:**
- README.md (if user-facing changes)
- LOCAL_DEVELOPMENT.md (if setup changes)
- DEPLOYMENT_AUTOMATION.md (if deployment changes)
- OPERATIONAL_RULES.md (if operational changes)
- This file (CLINE_WORKFLOWS.md) (if workflow changes)

**Documentation Standards:**
- Clear, step-by-step instructions
- Include code examples where helpful
- Reference related documentation
- Maintain consistent formatting
- Update cross-references

Please help me update all relevant documentation to reflect the changes made.
```

#### Maintenance Task Automation
```markdown
# Cline Prompt Template: Maintenance Automation

Help me automate maintenance tasks for the Easy RSVP application.

**Maintenance Task:**
[DESCRIBE_MAINTENANCE_TASK]

**Requirements:**
- Create shell scripts for automation
- Include error handling and logging
- Follow maintenance schedule requirements
- Integrate with existing monitoring

**Reference:**
- MAINTENANCE_SCHEDULE.md (maintenance procedures)
- OPERATIONAL_RULES.md (operational requirements)

**Script Requirements:**
1. Proper error handling
2. Comprehensive logging
3. Email notifications for failures
4. Rollback procedures if needed
5. Documentation of script usage

Please create the automation script and test it thoroughly.
```

## Advanced Cline Workflows

### 1. Feature Development Lifecycle

#### Complete Feature Development Process
```markdown
# Cline Workflow: Complete Feature Development

**Phase 1: Planning and Analysis**
1. Analyze feature requirements
2. Review existing codebase
3. Identify affected components
4. Plan database changes
5. Design API/interface changes

**Phase 2: Implementation**
1. Create database migrations
2. Update/create models
3. Implement controllers
4. Create/update views
5. Update routes
6. Add validation rules

**Phase 3: Testing**
1. Write unit tests
2. Write feature tests
3. Manual testing scenarios
4. Performance testing
5. Security testing

**Phase 4: Documentation**
1. Update code comments
2. Update API documentation
3. Update user documentation
4. Update deployment notes

**Phase 5: Deployment Preparation**
1. Pre-deployment validation
2. Staging environment testing
3. Production deployment plan
4. Rollback procedures

Use this workflow for any significant feature development.
```

### 2. Bug Fix Workflow

#### Systematic Bug Resolution
```markdown
# Cline Workflow: Bug Fix Process

**Step 1: Bug Reproduction**
1. Reproduce bug in local environment
2. Document exact steps to reproduce
3. Identify affected components
4. Check logs for error details

**Step 2: Root Cause Analysis**
1. Trace code execution path
2. Identify the failing component
3. Determine why it's failing
4. Check for related issues

**Step 3: Fix Implementation**
1. Implement minimal fix
2. Add/update tests to prevent regression
3. Verify fix resolves issue
4. Test for side effects

**Step 4: Validation**
1. Run full test suite
2. Manual testing of affected features
3. Performance impact assessment
4. Security impact review

**Step 5: Deployment**
1. Follow standard deployment process
2. Monitor post-deployment
3. Verify fix in production
4. Document resolution
```

### 3. Database Management Workflows

#### Database Schema Changes
```markdown
# Cline Workflow: Database Schema Management

**Planning Phase:**
1. Analyze current schema
2. Design new schema changes
3. Plan migration strategy
4. Consider backward compatibility
5. Plan rollback procedures

**Implementation Phase:**
1. Create migration files
2. Update model relationships
3. Update affected queries
4. Test migration up/down
5. Update seeders if needed

**Testing Phase:**
1. Test on fresh database
2. Test on copy of production data
3. Verify data integrity
4. Test application functionality
5. Performance impact testing

**Deployment Phase:**
1. Backup production database
2. Run migrations in production
3. Verify data integrity
4. Test application functionality
5. Monitor for issues
```

## Cline Best Practices

### 1. Effective Prompting Strategies

#### Context Provision
- Always provide project context (Laravel 12.0, MySQL, DreamHost hosting)
- Reference existing documentation files
- Specify environment (local/production)
- Include relevant file paths and code snippets

#### Task Specification
- Break complex tasks into smaller steps
- Specify expected outcomes
- Include validation criteria
- Provide examples when helpful

#### Error Handling
- Include error logs and symptoms
- Specify troubleshooting steps already taken
- Request systematic investigation approach
- Ask for multiple solution options

### 2. Code Quality Guidelines

#### Laravel Best Practices
- Follow Laravel conventions and patterns
- Use Eloquent relationships properly
- Implement proper validation
- Handle errors gracefully
- Use appropriate HTTP status codes

#### Security Considerations
- Validate all user input
- Use Laravel's built-in security features
- Protect against common vulnerabilities
- Follow principle of least privilege
- Encrypt sensitive data

#### Performance Optimization
- Optimize database queries
- Use appropriate caching strategies
- Minimize resource usage
- Monitor performance impact
- Consider scalability implications

### 3. Testing Strategies

#### Test Coverage
- Unit tests for models and services
- Feature tests for user workflows
- Integration tests for external services
- Performance tests for critical paths
- Security tests for sensitive operations

#### Test Environment
- Use separate test database
- Mock external dependencies
- Test with realistic data volumes
- Verify cleanup after tests
- Maintain test data consistency

## Integration with Project Documentation

### Cross-Reference Guide
- **LOCAL_DEVELOPMENT.md**: Local setup and testing procedures
- **DEPLOYMENT_AUTOMATION.md**: Production deployment workflows
- **OPERATIONAL_RULES.md**: Operational procedures and standards
- **MAINTENANCE_SCHEDULE.md**: Regular maintenance and monitoring
- **TROUBLESHOOTING.md**: Problem resolution procedures
- **SECURITY_NOTES.md**: Security guidelines and best practices

### Workflow Dependencies
1. **Development** → LOCAL_DEVELOPMENT.md procedures
2. **Testing** → Local testing and validation
3. **Deployment** → DEPLOYMENT_AUTOMATION.md procedures
4. **Monitoring** → MAINTENANCE_SCHEDULE.md procedures
5. **Issues** → TROUBLESHOOTING.md procedures

## Continuous Improvement

### Workflow Optimization
- Regularly review and update workflows
- Incorporate lessons learned
- Optimize for efficiency and accuracy
- Share successful patterns with team
- Document workflow improvements

### AI Assistance Evolution
- Refine prompts based on results
- Develop project-specific templates
- Create reusable workflow components
- Build institutional knowledge
- Train team on effective AI usage

---

**Usage Guidelines**: These workflows should be adapted to specific tasks and contexts. Always verify AI-generated code and follow established project standards.

**Maintenance**: This document should be updated as workflows evolve and new patterns emerge. Regular review ensures continued effectiveness.

**Related Documentation**:
- [LOCAL_DEVELOPMENT.md](LOCAL_DEVELOPMENT.md) - Local development setup and procedures
- [DEPLOYMENT_AUTOMATION.md](DEPLOYMENT_AUTOMATION.md) - Automated deployment procedures
- [OPERATIONAL_RULES.md](OPERATIONAL_RULES.md) - Operational procedures and standards
- [MAINTENANCE_SCHEDULE.md](MAINTENANCE_SCHEDULE.md) - Regular maintenance procedures
- [TROUBLESHOOTING.md](TROUBLESHOOTING.md) - Problem resolution procedures
