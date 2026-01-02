# Panduan Deployment Simple Akunting v5

Panduan lengkap untuk deployment aplikasi Simple Akunting v5 ke server produksi.

## 📋 Persyaratan Server

### Server Minimum
- **OS**: Ubuntu 20.04 LTS / 22.04 LTS atau CentOS 7/8
- **RAM**: 1 GB (disarankan 2 GB+)
- **Storage**: 10 GB (disarankan 20 GB+)
- **CPU**: 1 Core (disarankan 2+ Core)

### Software Requirements
- PHP 8.3 atau lebih tinggi
- MySQL 8.0+ atau MariaDB 10.3+
- Nginx atau Apache
- Composer
- Node.js 20.x+
- Git
- Supervisor (optional, untuk queue)

### PHP Extensions Required
```bash
php8.3-cli
php8.3-fpm
php8.3-mysql
php8.3-mbstring
php8.3-xml
php8.3-bcmath
php8.3-curl
php8.3-zip
php8.3-gd
php8.3-intl
```

---

## 🚀 Deployment ke Server Produksi

### 1. Persiapan Server

#### a. Update System
```bash
sudo apt update && sudo apt upgrade -y
```

#### b. Install PHP 8.3
```bash
# Tambah repository PHP
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP dan extensions
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring \
                    php8.3-xml php8.3-bcmath php8.3-curl php8.3-zip php8.3-gd \
                    php8.3-intl php8.3-redis

# Verifikasi instalasi
php -v
```

#### c. Install MySQL 8.0
```bash
# Install MySQL Server
sudo apt install -y mysql-server

# Secure installation
sudo mysql_secure_installation

# Login ke MySQL
sudo mysql -u root -p

# Buat database dan user
CREATE DATABASE simpleakunting CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'simpleakun_user'@'localhost' IDENTIFIED BY 'password_yang_kuat';
GRANT ALL PRIVILEGES ON simpleakunting.* TO 'simpleakun_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### d. Install Composer
```bash
cd ~
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

#### e. Install Node.js & NPM
```bash
# Install Node.js 20.x
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Verifikasi
node -v
npm -v
```

#### f. Install Nginx
```bash
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

---

### 2. Clone Repository

```bash
# Buat direktori aplikasi
sudo mkdir -p /var/www/simpleakunting
cd /var/www/simpleakunting

# Clone repository
sudo git clone https://github.com/solusigroup/simpleakunting-v5.git .

# Set ownership
sudo chown -R www-data:www-data /var/www/simpleakunting
sudo chmod -R 755 /var/www/simpleakunting
```

---

### 3. Konfigurasi Aplikasi

#### a. Install Dependencies
```bash
cd /var/www/simpleakunting

# Install PHP dependencies
sudo -u www-data composer install --optimize-autoloader --no-dev

# Install Node dependencies
sudo -u www-data npm ci --omit=dev
```

#### b. Setup Environment
```bash
# Copy .env.example
sudo -u www-data cp .env.example .env

# Generate application key
sudo -u www-data php artisan key:generate

# Edit .env file
sudo nano .env
```

Sesuaikan konfigurasi di file `.env`:
```env
APP_NAME="Simple Akunting"
APP_ENV=production
APP_KEY=base64:xxx # sudah auto-generate
APP_DEBUG=false
APP_URL=https://domain-anda.com

LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simpleak_v5
DB_USERNAME=simpleak_v5user
DB_PASSWORD=password_yang_kuat

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Email Configuration (optional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

#### c. Migrasi Database dan Seeding

```bash
# Run migrations
sudo -u www-data php artisan migrate --force
```

##### Database Seeding (Mengisi Data Awal)

Aplikasi menyediakan beberapa seeder untuk mengisi data awal. Pilih seeder yang sesuai dengan jenis usaha Anda:

**1. Seeder Chart of Accounts (COA)**

Pilih salah satu sesuai jenis usaha:

```bash
# Option A: COA untuk Perusahaan Dagang/Retail
sudo -u www-data php artisan db:seed --class=CoaDagangSeeder

# Option B: COA untuk Koperasi Simpan Pinjam
sudo -u www-data php artisan db:seed --class=CoaSimpanPinjamSeeder

# Option C: COA Template Lengkap (Recommended - multi-purpose)
sudo -u www-data php artisan db:seed --class=CoaTemplateSeeder
```

**2. Seeder Data Awal Perusahaan**

```bash
# Isi data profil perusahaan default
sudo -u www-data php artisan db:seed --class=PerusahaanSeeder
```

**3. Seeder Jenis Simpan Pinjam** (jika menggunakan modul koperasi)

```bash
# Isi jenis simpanan dan pinjaman default
sudo -u www-data php artisan db:seed --class=JenisSimpanPinjamSeeder
```

**4. Jalankan Semua Seeder Sekaligus**

```bash
# Run all seeders yang terdaftar di DatabaseSeeder
sudo -u www-data php artisan db:seed
```

##### Penjelasan Seeder

| Seeder | Deskripsi | Kapan Digunakan |
|--------|-----------|-----------------|
| `CoaTemplateSeeder` | COA lengkap untuk berbagai jenis usaha | **Recommended** - Untuk instalasi baru, paling fleksibel |
| `CoaDagangSeeder` | COA khusus perusahaan dagang | Jika fokus pada usaha dagang/retail |
| `CoaSimpanPinjamSeeder` | COA khusus koperasi | Jika fokus pada koperasi simpan pinjam |
| `PerusahaanSeeder` | Data profil perusahaan | Selalu jalankan untuk setup awal |
| `JenisSimpanPinjamSeeder` | Jenis simpanan & pinjaman | Hanya jika menggunakan modul koperasi |

##### Fresh Install (HATI-HATI!)

Jika ingin reset database dan seed ulang (akan **MENGHAPUS SEMUA DATA**):

```bash
# WARNING: Ini akan drop semua tabel dan recreate!
sudo -u www-data php artisan migrate:fresh --seed
```

##### Membuat User Superuser

Setelah database ter-setup, buat user admin pertama:

```bash
# Via Tinker
sudo -u www-data php artisan tinker
```

Kemudian jalankan di tinker:

```php
\App\Models\User::create([
    'nama_user' => 'admin',
    'password_hash' => bcrypt('password_anda'),
    'role' => 'superuser',
    'jabatan' => 'Administrator'
]);
exit
```

Atau via SQL langsung:

```sql
INSERT INTO users (nama_user, password_hash, role, jabatan, created_at, updated_at) 
VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: 'password'
    'superuser',
    'Administrator',
    NOW(),
    NOW()
);
```

> **Note**: Hash di atas adalah untuk password `'password'`. Untuk production, gunakan password yang kuat!


#### d. Build Assets
```bash
sudo -u www-data npm run build
```

#### e. Optimize Application
```bash
# Cache configuration
sudo -u www-data php artisan config:cache

# Cache routes
sudo -u www-data php artisan route:cache

# Cache views
sudo -u www-data php artisan view:cache

# Optimize autoloader
sudo -u www-data composer dumpautoload -o
```

---

### 4. Set File Permissions

```bash
# Set proper ownership
sudo chown -R www-data:www-data /var/www/simpleakunting

# Set directory permissions
sudo find /var/www/simpleakunting -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/simpleakunting -type f -exec chmod 644 {} \;

# Storage dan cache harus writable
sudo chmod -R 775 /var/www/simpleakunting/storage
sudo chmod -R 775 /var/www/simpleakunting/bootstrap/cache
```

---

### 5. Konfigurasi Web Server

#### Option A: Nginx (Recommended)

Buat file konfigurasi:
```bash
sudo nano /etc/nginx/sites-available/simpleakunting
```

Isi dengan:
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name domain-anda.com www.domain-anda.com;
    root /var/www/simpleakunting/public;

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
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Security headers
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "strict-origin-when-cross-origin";
    
    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/json;
}
```

Enable site:
```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/simpleakunting /etc/nginx/sites-enabled/

# Remove default site
sudo rm /etc/nginx/sites-enabled/default

# Test configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

#### Option B: Apache

```bash
# Enable required modules
sudo a2enmod rewrite
sudo a2enmod ssl
sudo a2enmod headers

# Create virtual host
sudo nano /etc/apache2/sites-available/simpleakunting.conf
```

Isi dengan:
```apache
<VirtualHost *:80>
    ServerName domain-anda.com
    ServerAlias www.domain-anda.com
    ServerAdmin admin@domain-anda.com
    DocumentRoot /var/www/simpleakunting/public

    <Directory /var/www/simpleakunting/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/simpleakunting-error.log
    CustomLog ${APACHE_LOG_DIR}/simpleakunting-access.log combined

    # Security headers
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
</VirtualHost>
```

Enable site:
```bash
# Enable site
sudo a2ensite simpleakunting.conf

# Disable default site
sudo a2dissite 000-default.conf

# Restart Apache
sudo systemctl restart apache2
```

---

### 6. Setup SSL/HTTPS dengan Let's Encrypt

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Generate SSL certificate (untuk Nginx)
sudo certbot --nginx -d domain-anda.com -d www.domain-anda.com

# Atau untuk Apache
# sudo certbot --apache -d domain-anda.com -d www.domain-anda.com

# Auto-renewal test
sudo certbot renew --dry-run
```

Certbot akan otomatis mengupdate konfigurasi Nginx/Apache dengan SSL.

---

### 7. Setup Queue Worker (Optional)

Untuk background jobs:

```bash
# Install Supervisor
sudo apt install -y supervisor

# Buat konfigurasi worker
sudo nano /etc/supervisor/conf.d/simpleakunting-worker.conf
```

Isi dengan:
```ini
[program:simpleakunting-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/simpleakunting/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/simpleakunting/storage/logs/worker.log
stopwaitsecs=3600
```

Start worker:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start simpleakunting-worker:*
```

---

### 8. Setup Cron Jobs

```bash
# Edit crontab
sudo crontab -e -u www-data
```

Tambahkan:
```cron
* * * * * cd /var/www/simpleakunting && php artisan schedule:run >> /dev/null 2>&1
```

---

### 9. Firewall Configuration

```bash
# Install UFW
sudo apt install -y ufw

# Allow SSH, HTTP, HTTPS
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'  # atau 'Apache Full' jika pakai Apache

# Enable firewall
sudo ufw enable

# Check status
sudo ufw status
```

---

## 🔄 Update Aplikasi

Untuk update aplikasi ke versi terbaru:

```bash
cd /var/www/simpleakunting

# Backup database
sudo -u www-data php artisan backup:run  # jika sudah setup backup

# Masuk ke maintenance mode
sudo -u www-data php artisan down

# Pull latest code
sudo -u www-data git pull origin main

# Update dependencies
sudo -u www-data composer install --optimize-autoloader --no-dev
sudo -u www-data npm ci --omit=dev

# Build assets
sudo -u www-data npm run build

# Run migrations
sudo -u www-data php artisan migrate --force

# Clear and rebuild cache
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan route:clear

# Rebuild cache
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

# Keluar dari maintenance mode
sudo -u www-data php artisan up

# Restart PHP-FPM
sudo systemctl restart php8.3-fpm

# Restart queue workers (jika ada)
sudo supervisorctl restart simpleakunting-worker:*
```

---

## 🔍 Troubleshooting

### Error 500 - Internal Server Error

1. **Cek Error Log**:
   ```bash
   # Laravel logs
   tail -f /var/www/simpleakunting/storage/logs/laravel.log
   
   # Nginx error logs
   sudo tail -f /var/log/nginx/error.log
   
   # PHP-FPM logs
   sudo tail -f /var/log/php8.3-fpm.log
   ```

2. **Permission Issues**:
   ```bash
   sudo chown -R www-data:www-data /var/www/simpleakunting
   sudo chmod -R 775 /var/www/simpleakunting/storage
   sudo chmod -R 775 /var/www/simpleakunting/bootstrap/cache
   ```

3. **Clear Cache**:
   ```bash
   sudo -u www-data php artisan config:clear
   sudo -u www-data php artisan cache:clear
   sudo -u www-data php artisan view:clear
   ```

### Database Connection Error

1. Cek kredensial di `.env`
2. Pastikan MySQL running: `sudo systemctl status mysql`
3. Test koneksi: `mysql -u simpleakun_user -p simpleakunting`

### Assets Not Loading

1. Cek symbolic link: `ls -la /var/www/simpleakunting/public/storage`
2. Create link jika belum ada: `sudo -u www-data php artisan storage:link`
3. Rebuild assets: `sudo -u www-data npm run build`

### Performance Issues

1. **Enable OPcache** di `php.ini`:
   ```ini
   opcache.enable=1
   opcache.memory_consumption=128
   opcache.interned_strings_buffer=8
   opcache.max_accelerated_files=10000
   opcache.revalidate_freq=2
   ```

2. **Optimize Laravel**:
   ```bash
   sudo -u www-data php artisan optimize
   ```

3. **Database Indexing**: Tambah index pada tabel besar

---

## 📊 Monitoring

### Server Monitoring
- Install `htop` untuk monitor resources
- Setup `fail2ban` untuk security
- Monitor disk space: `df -h`

### Application Monitoring
- Laravel Logs: `/var/www/simpleakunting/storage/logs/`
- Database Size: `sudo du -sh /var/lib/mysql/simpleakunting`

### Backup Strategy

Setup automated backup:

```bash
# Backup database
sudo -u www-data php artisan backup:run

# Backup files
sudo tar -czf backup-$(date +%Y%m%d).tar.gz /var/www/simpleakunting
```

Simpan backup di lokasi terpisah atau cloud storage.

---

## 🔒 Security Checklist

- [ ] Update system secara berkala
- [ ] Gunakan HTTPS (SSL)
- [ ] Set `APP_DEBUG=false` di production
- [ ] Gunakan password database yang kuat
- [ ] Aktifkan firewall (UFW)
- [ ] Setup fail2ban
- [ ] Backup database rutin
- [ ] Monitor log files
- [ ] Update dependencies: `composer update`, `npm update`
- [ ] Disable directory listing
- [ ] Set proper file permissions (755/644)

---

## 📞 Support

Jika mengalami masalah saat deployment:
- Email: kurniawan@petalmail.com
- GitHub Issues: https://github.com/solusigroup/simpleakunting-v5/issues

---

**Happy Deploying!** 🚀
