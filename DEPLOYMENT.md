# 🚀 Deploy Multi-Tenant SimpleAkunting v5

## Informasi Server

| Item | Detail |
|------|--------|
| **VPS** | Mentari VPS |
| **IP** | 172.16.30.204 |
| **Domain Utama** | v5.simpleakunting.id |
| **Subdomain Tenant** | `{tenant}.v5.simpleakunting.id` |
| **Contoh Tenant** | `tenant_demo.v5.simpleakunting.id` |
| **Database Central** | `dbv5_central` |
| **Database Tenant** | `dbv5_{tenant_id}` (contoh: `dbv5_tenantdemo`) |

---

## 1. Setup DNS

Di panel DNS `simpleakunting.id`, tambahkan:

```
Tipe    Nama                       Value            TTL
A       v5.simpleakunting.id       172.16.30.204    Auto
A       *.v5.simpleakunting.id     172.16.30.204    Auto
```

> ⚠️ Record wildcard `*.v5` WAJIB agar subdomain tenant bisa diakses.

---

## 2. Install Dependencies

```bash
sudo apt update && sudo apt upgrade -y

# PHP 8.2 + extensions
sudo apt install -y php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml \
    php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd \
    nginx mysql-server git unzip

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Node.js 18
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

---

## 3. Setup MySQL

```bash
sudo mysql -u root
```

```sql
-- Database central
CREATE DATABASE dbv5_central CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- User dengan GRANT OPTION (wajib untuk auto-create tenant DB)
CREATE USER 'dbv5_user'@'localhost' IDENTIFIED BY 'PASSWORD_KUAT';
GRANT ALL PRIVILEGES ON *.* TO 'dbv5_user'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
EXIT;
```

> ⚠️ User MySQL **HARUS** punya `GRANT OPTION` pada `*.*` agar stancl/tenancy bisa otomatis membuat database baru untuk setiap tenant.

---

## 4. Clone & Setup Aplikasi

```bash
cd /var/www
sudo git clone https://github.com/solusigroup/simpleakunting-v5.git simpleakunting
sudo chown -R www-data:www-data simpleakunting
cd simpleakunting

composer install --no-dev --optimize-autoloader
npm install && npm run build

cp .env.example .env
php artisan key:generate
```

### Edit `.env`:

```env
APP_NAME="SimpleAkunting v5"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://v5.simpleakunting.id

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dbv5_central
DB_USERNAME=dbv5_user
DB_PASSWORD=PASSWORD_KUAT

# Tenancy
TENANCY_ENABLED=true
TENANCY_CENTRAL_DOMAINS=v5.simpleakunting.id
TENANCY_DB_DATABASE=dbv5_central
TENANCY_DB_USERNAME=dbv5_user
TENANCY_DB_PASSWORD=PASSWORD_KUAT

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Jalankan Migrasi Central:

```bash
php artisan migrate --path=database/migrations --database=central --force

sudo chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

---

## 5. Konfigurasi Nginx

### Wildcard SSL (Let's Encrypt):

```bash
sudo apt install -y certbot python3-certbot-nginx

# Wildcard SSL via DNS challenge
sudo certbot certonly --manual --preferred-challenges=dns \
    -d v5.simpleakunting.id -d "*.v5.simpleakunting.id"

# Ikuti instruksi: tambahkan TXT record _acme-challenge.v5.simpleakunting.id
```

### File: `/etc/nginx/sites-available/simpleakunting`

```nginx
server {
    listen 80;
    server_name v5.simpleakunting.id *.v5.simpleakunting.id;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name v5.simpleakunting.id *.v5.simpleakunting.id;

    root /var/www/simpleakunting/public;
    index index.php;

    ssl_certificate     /etc/letsencrypt/live/v5.simpleakunting.id/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/v5.simpleakunting.id/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;

    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known) {
        deny all;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/simpleakunting /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 6. Membuat Tenant

### Via Tinker (contoh: tenant_demo)

```bash
php artisan tinker
```

```php
$tenant = App\Models\Tenant::create([
    'id' => 'tenantdemo',
    'nama_perusahaan' => 'PT Demo Sejahtera',
    'email' => 'admin@demo.com',
]);

$tenant->domains()->create([
    'domain' => 'tenant_demo.v5.simpleakunting.id',
]);
```

Hasil otomatis:
- ✅ Database `dbv5_tenantdemo` dibuat
- ✅ Semua tabel bisnis di-migrate
- ✅ Chart of Accounts (CoA) di-seed
- ✅ User default dibuat

### Via Admin Panel (Browser)

1. Buka `https://v5.simpleakunting.id`
2. Klik **"Daftar Perusahaan"**
3. Isi Tenant ID: `tenantdemo`, Nama: `PT Demo Sejahtera`
4. Klik **"Buat Tenant"**

### Login ke Tenant:

```
URL:      https://tenant_demo.v5.simpleakunting.id/login
Username: admin
Password: password
```

> ⚠️ Ganti password default segera setelah login pertama!

---

## 7. Manajemen Tenant

| Perintah | Fungsi |
|----------|--------|
| `php artisan tenants:migrate --force` | Migrate semua tenant |
| `php artisan tenants:migrate --tenants=tenantdemo --force` | Migrate tenant tertentu |
| `php artisan tenants:seed --tenants=tenantdemo --force` | Seed ulang tenant |

### Hapus Tenant:

```php
// via tinker
$tenant = App\Models\Tenant::find('tenantdemo');
$tenant->delete(); // DB dbv5_tenantdemo otomatis dihapus
```

### Nonaktifkan Tenant (tanpa hapus):

```php
App\Models\Tenant::find('tenantdemo')->update(['is_active' => false]);
```

---

## 8. Backup Database

```bash
#!/bin/bash
# /var/www/simpleakunting/backup.sh
DATE=$(date +%Y%m%d_%H%M%S)
DIR="/var/backups/simpleakunting"
mkdir -p $DIR

# Central
mysqldump -u dbv5_user -p dbv5_central > "$DIR/central_$DATE.sql"

# Semua tenant
for DB in $(mysql -u dbv5_user -p -N -e "SHOW DATABASES LIKE 'dbv5_%'" | grep -v central); do
    mysqldump -u dbv5_user -p "$DB" > "$DIR/${DB}_$DATE.sql"
done

echo "Backup selesai: $DIR"
```

```bash
# Cron: backup setiap hari jam 2 pagi
echo "0 2 * * * /var/www/simpleakunting/backup.sh" | sudo crontab -
```

---

## 9. Update Aplikasi

```bash
cd /var/www/simpleakunting
git pull origin main
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Migrate central + tenant
php artisan migrate --path=database/migrations --database=central --force
php artisan tenants:migrate --force

# Clear cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

sudo systemctl reload php8.2-fpm
```

---

## Arsitektur

```
v5.simpleakunting.id (central)                → Landing Page + Admin Panel
tenant_demo.v5.simpleakunting.id (tenant)      → DB: dbv5_tenantdemo
perusahaan_a.v5.simpleakunting.id (tenant)     → DB: dbv5_perusahaana
koperasi_xyz.v5.simpleakunting.id (tenant)     → DB: dbv5_koperasixyz
```

---

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Subdomain tidak bisa diakses | Cek DNS wildcard `*.v5.simpleakunting.id` → `172.16.30.204` |
| "Could not create database" | MySQL user harus punya `GRANT OPTION` pada `*.*` |
| "Tenant not found" | Domain belum didaftarkan di tabel `domains` |
| 419 Page Expired | `php artisan config:cache`, set `SESSION_DOMAIN=.v5.simpleakunting.id` |
| Asset/CSS tidak muncul | Jalankan `npm run build` |
