# Deployment Guide

## Requirements

- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+
- Composer
- Node.js 16+
- NPM

## Production Deployment

### 1. Server Setup

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP
sudo apt install php8.1 php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip

# Install MySQL
sudo apt install mysql-server

# Install Nginx
sudo apt install nginx

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### 2. Clone & Install

```bash
# Clone repository
cd /var/www
git clone <repo-url> payment
cd payment

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 3. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Edit .env
nano .env
```

**Production .env:**
```env
APP_NAME="Payment System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://payment.example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=payment_prod
DB_USERNAME=payment_user
DB_PASSWORD=<strong-password>

QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_DRIVER=file

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=<app-password>
MAIL_ENCRYPTION=tls
```

### 4. Database Setup

```bash
# Create database
mysql -u root -p
CREATE DATABASE payment_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'payment_user'@'localhost' IDENTIFIED BY '<strong-password>';
GRANT ALL PRIVILEGES ON payment_prod.* TO 'payment_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Run migrations
php artisan migrate --force

# Seed initial data
php artisan db:seed --force

# Create storage link
php artisan storage:link
```

### 5. Nginx Configuration

```nginx
server {
    listen 80;
    server_name payment.example.com;
    root /var/www/payment/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/payment /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 6. SSL Certificate

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Get certificate
sudo certbot --nginx -d payment.example.com

# Auto-renewal
sudo certbot renew --dry-run
```

### 7. Queue Worker

```bash
# Create supervisor config
sudo nano /etc/supervisor/conf.d/payment-worker.conf
```

```ini
[program:payment-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/payment/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/payment/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Start worker
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start payment-worker:*
```

### 8. Cron Jobs

```bash
# Edit crontab
sudo crontab -e -u www-data

# Add Laravel scheduler
* * * * * cd /var/www/payment && php artisan schedule:run >> /dev/null 2>&1
```

### 9. Optimization

```bash
# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

## Backup Strategy

### Database Backup

```bash
# Create backup script
nano /usr/local/bin/backup-payment.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/payment"
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u payment_user -p<password> payment_prod > $BACKUP_DIR/db_$DATE.sql

# Compress
gzip $BACKUP_DIR/db_$DATE.sql

# Keep only last 30 days
find $BACKUP_DIR -name "db_*.sql.gz" -mtime +30 -delete
```

```bash
# Make executable
chmod +x /usr/local/bin/backup-payment.sh

# Add to crontab
0 2 * * * /usr/local/bin/backup-payment.sh
```

## Monitoring

### Log Monitoring

```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# View Nginx logs
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log

# View PHP-FPM logs
tail -f /var/log/php8.1-fpm.log
```

### Health Check

```bash
# Create health check endpoint
php artisan make:controller HealthController
```

## Rollback

```bash
# Rollback last migration
php artisan migrate:rollback

# Rollback to specific version
git checkout <previous-version>
composer install
npm run build
php artisan migrate
php artisan cache:clear
```

## Security Checklist

- [ ] Change default passwords
- [ ] Enable firewall (UFW)
- [ ] Setup fail2ban
- [ ] Regular security updates
- [ ] Backup encryption
- [ ] SSL certificate
- [ ] Disable directory listing
- [ ] Hide server version
- [ ] Rate limiting
- [ ] CSRF protection enabled

## Performance Tuning

### PHP-FPM

```ini
; /etc/php/8.1/fpm/pool.d/www.conf
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
```

### MySQL

```ini
; /etc/mysql/my.cnf
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
max_connections = 200
query_cache_size = 64M
```

### Redis (Optional)

```bash
# Install Redis
sudo apt install redis-server

# Update .env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

## Troubleshooting

### 500 Error
- Check `storage/logs/laravel.log`
- Verify file permissions
- Clear cache

### Queue not processing
- Check supervisor status: `sudo supervisorctl status`
- Restart worker: `sudo supervisorctl restart payment-worker:*`

### Database connection error
- Verify credentials in `.env`
- Check MySQL service: `sudo systemctl status mysql`

## Maintenance Mode

```bash
# Enable maintenance
php artisan down --secret="secret-token"

# Disable maintenance
php artisan up
```
