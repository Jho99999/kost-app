# Sistem Informasi Kost — Panduan Instalasi

## Prasyarat
- PHP 8.2+, Composer, Node.js 18+
- MySQL (Laragon/XAMPP) atau Railway MySQL plugin
- Laragon (Windows) atau MAMP/Herd (Mac)

---

## Cara Instalasi (Lokal — Laragon)

### 1. Buat project Laravel baru
```bash
composer create-project laravel/laravel kost-app
cd kost-app
```

### 2. Salin semua file dari zip ini
Extract `kost-complete.zip` lalu **salin semua isinya ke dalam folder `kost-app/`**.
Izinkan penimpaan file yang sudah ada.

### 3. Install dependencies
```bash
composer install
npm install
```

### 4. Buat database
Buka phpMyAdmin → buat database bernama `kost_db`.

### 5. Konfigurasi .env
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
APP_NAME="Sistem Informasi Kost"
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kost_db
DB_USERNAME=root
DB_PASSWORD=

# Email — gunakan Mailtrap untuk testing
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=xxxxx
MAIL_PASSWORD=xxxxx
MAIL_FROM_ADDRESS=noreply@kost.com
MAIL_FROM_NAME="Sistem Informasi Kost"
```

### 6. Jalankan migrasi dan seeder
```bash
php artisan migrate
php artisan db:seed
```

### 7. Buat symlink storage (untuk foto kamar dan bukti bayar)
```bash
php artisan storage:link
```

### 8. Build assets dan jalankan
```bash
npm run build
php artisan serve
```

Buka http://localhost:8000

**Login admin:** `admin@kost.com` / `admin123`

---

## Deployment ke Railway

### 1. Push project ke GitHub (tanpa folder vendor/ dan node_modules/)

### 2. Buat Railway project → tambah MySQL plugin

### 3. Set Environment Variables di Railway:
```
APP_NAME=Sistem Informasi Kost
APP_ENV=production
APP_DEBUG=false
APP_KEY=           ← php artisan key:generate --show
APP_TIMEZONE=Asia/Jakarta

DB_HOST=${{MySQL.MYSQL_HOST}}
DB_PORT=${{MySQL.MYSQL_PORT}}
DB_DATABASE=${{MySQL.MYSQL_DATABASE}}
DB_USERNAME=${{MySQL.MYSQL_USER}}
DB_PASSWORD=${{MySQL.MYSQL_PASSWORD}}

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=xxxxx
MAIL_PASSWORD=xxxxx
MAIL_FROM_ADDRESS=noreply@kost.com
MAIL_FROM_NAME="Sistem Informasi Kost"
```

### 4. Jalankan seeder via Railway Shell (sekali saja setelah deploy pertama):
```bash
php artisan db:seed
```

### 5. Setup Cron Job di Railway (Settings → Cron Jobs):
```
* * * * * php artisan schedule:run
```

> **Catatan storage Railway:** File foto dan bukti bayar bersifat *ephemeral*
> (hilang saat redeploy). Untuk production nyata, ganti ke Cloudflare R2 atau
> Supabase Storage. Untuk keperluan skripsi/demo, storage lokal sudah cukup.

---

## Struktur Aplikasi

```
Aktor: Admin (pemilik kost) dan Penyewa (penghuni)

Alur utama:
  Penyewa daftar → lihat kamar → ajukan booking
  Admin setujui  → sistem generate tagihan bulanan otomatis
  Penyewa upload bukti bayar setiap bulan
  Admin verifikasi → tagihan lunas

Cron harian:
  00:05 → tandai tagihan overdue (lewat jatuh tempo)
  08:00 → kirim email reminder H-7 kepada penyewa
```

## Akun Default (setelah db:seed)
| Role  | Email             | Password |
|-------|-------------------|----------|
| Admin | admin@kost.com    | admin123 |

Kamar contoh: 6 kamar (2 Standard, 2 Deluxe, 2 VIP) sudah tersedia.
