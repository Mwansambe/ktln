# Chapter 17: Deployment and Production Maintenance

## Introduction
Building features is only half the work. This chapter teaches how to deploy Paperchase safely and maintain it after launch.

## Learning Objectives
By the end of this chapter, you can:
- Prepare a secure production environment
- Deploy Laravel with correct optimization steps
- Configure workers, cache, logs, and backups
- Maintain the system after launch

## 1. Production Requirements
- PHP 8.2+
- Composer
- PostgreSQL 15+
- Web server (Nginx/Apache)
- Process manager (Supervisor/systemd)
- Optional: Redis for cache/queues

## 2. Environment Setup
In `.env` production:
- `APP_ENV=production`
- `APP_DEBUG=false`
- Strong DB credentials
- Secure `APP_KEY`
- Correct `APP_URL`

Never commit `.env` to git.

## 3. Deployment Steps

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm ci
npm run build
```

Set directory permissions for:
- `storage/`
- `bootstrap/cache/`

## 4. Queue and Scheduler Setup
Queue worker (Supervisor recommended):

```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

Scheduler cron entry:

```bash
* * * * * cd /var/www/paperchaseapi && php artisan schedule:run >> /dev/null 2>&1
```

## 5. Database and File Backups
Automate backups:
- PostgreSQL daily dump
- storage files backup
- offsite backup retention (S3 or equivalent)

Test restore monthly. A backup is only valid if restore works.

## 6. Monitoring and Alerting
Track:
- 5xx error rate
- queue failures
- response times
- disk usage
- failed login spikes

Store logs in centralized logging if possible.

## 7. Post-Launch Maintenance Plan
Weekly:
- review logs
- check failed jobs
- verify backups

Monthly:
- dependency updates
- security patch review
- DB index and slow query review

Quarterly:
- disaster recovery drill
- permission audit

## 8. Deployment Rollback Strategy
If deployment fails:
1. Stop incoming traffic or put app in maintenance mode
2. Revert code to previous release tag
3. Roll back migration only if safe and planned
4. Clear/rebuild caches
5. Verify health endpoints before reopening traffic

## Hands-On Exercise
1. Create a deployment checklist document for your server.
2. Configure a staging environment that mirrors production.
3. Add a health-check endpoint returning DB and storage status.
4. Create and test a backup + restore script.

## Challenge Extension
Set up zero-downtime deployment with symlinked releases and queue worker restart hooks.

## Summary
Paperchase can now be deployed and maintained with production discipline. In the final chapter, you will build troubleshooting habits for rapid debugging and recovery.
