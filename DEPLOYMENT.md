# Deployment Guide - Backend AC

## 🚀 Production Deployment

### 1. Server Requirements
- PHP 8.2+
- MySQL 8.0+ atau PostgreSQL 13+
- Composer
- Web Server (Apache/Nginx)
- SSL Certificate

### 2. Environment Setup

```bash
# Clone repository
git clone <repository-url> backend-ac
cd backend-ac

# Install dependencies
composer install --optimize-autoloader --no-dev

# Setup environment
cp .env.example .env
php artisan key:generate
```

### 3. Database Configuration

Edit `.env` file:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absolute_cinema
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Sanctum Configuration
SANCTUM_STATEFUL_DOMAINS=yourdomain.com,www.yourdomain.com
SESSION_DOMAIN=.yourdomain.com
```

### 4. Database Migration

```bash
# Run migrations
php artisan migrate --force

# Seed initial data (optional for production)
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=StudioSeeder
php artisan db:seed --class=PriceSeeder
```

### 5. Optimization

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

### 6. Web Server Configuration

#### Apache Virtual Host
```apache
<VirtualHost *:443>
    ServerName api.yourdomain.com
    DocumentRoot /path/to/backend-ac/public
    
    SSLEngine on
    SSLCertificateFile /path/to/certificate.crt
    SSLCertificateKeyFile /path/to/private.key
    
    <Directory /path/to/backend-ac/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    # Security Headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
</VirtualHost>
```

#### Nginx Configuration
```nginx
server {
    listen 443 ssl;
    server_name api.yourdomain.com;
    root /path/to/backend-ac/public;
    
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    
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
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 7. Security Checklist

- [ ] Set `APP_DEBUG=false`
- [ ] Use strong `APP_KEY`
- [ ] Configure proper database credentials
- [ ] Set up SSL certificate
- [ ] Configure firewall rules
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Secure storage directory
- [ ] Configure backup strategy

### 8. Monitoring & Logging

```bash
# Setup log rotation
sudo nano /etc/logrotate.d/laravel

# Content:
/path/to/backend-ac/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    notifempty
    create 644 www-data www-data
}
```

### 9. Backup Strategy

```bash
# Database backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u username -p database_name > backup_$DATE.sql
```

### 10. Health Check

Create monitoring endpoint:
```bash
# Test API health
curl -X GET https://api.yourdomain.com/up
```

## 🔧 Local Development

### Quick Start
```bash
# Clone and setup
git clone <repository-url> backend-ac
cd backend-ac
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Run development server
php artisan serve
```

### Testing
```bash
# Run tests
php artisan test

# Test API endpoints
php test_api.php
```

## 📞 Support

For deployment issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check web server error logs
3. Verify database connection
4. Test API endpoints with Postman collection