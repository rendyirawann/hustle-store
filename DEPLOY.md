# 🚀 Panduan Deploy HustleStore ke `vip.hustlesync.my.id`

Panduan ini untuk **VPS Ubuntu 22.04 / 24.04** (akses root/SSH) dengan stack
**Nginx + PHP 8.3-FPM + PostgreSQL**. Semua perintah bisa langsung di-copy.

| Item | Nilai |
|---|---|
| Subdomain | `vip.hustlesync.my.id` |
| IP server (A record) | `76.13.23.58` |
| Folder aplikasi | `/var/www/hustlestore` |
| Database | PostgreSQL (wajib — query katalog memakai `ILIKE`, **tidak jalan di MySQL**) |

> ⚠️ **Shared hosting (cPanel/hPanel) yang hanya punya MySQL tidak bisa dipakai.** Gunakan VPS,
> atau hosting yang menyediakan PostgreSQL + akses SSH + cron.

---

## 1. DNS subdomain

Record ini sudah dibuat di panel domain:

```
vip.hustlesync.my.id.   14400   IN   A   76.13.23.58
```

Cek dari laptop (PowerShell). Hasilnya harus `76.13.23.58`. TTL 14400 berarti propagasi bisa sampai ±4 jam.

```powershell
nslookup vip.hustlesync.my.id
```

---

## 2. Siapkan server (sekali saja)

SSH ke server: `ssh root@76.13.23.58`

```bash
apt update && apt upgrade -y
apt install -y nginx postgresql unzip git curl \
  php8.3-fpm php8.3-cli php8.3-pgsql php8.3-mbstring php8.3-xml php8.3-curl \
  php8.3-zip php8.3-bcmath php8.3-intl php8.3-gd php8.3-redis \
  composer certbot python3-certbot-nginx

# Firewall
ufw allow OpenSSH && ufw allow 'Nginx Full' && ufw --force enable
```

> Ubuntu 22.04 belum punya PHP 8.3 bawaan. Tambahkan dulu repo-nya:
> `add-apt-repository ppa:ondrej/php -y && apt update`, lalu jalankan `apt install` di atas.

### Database PostgreSQL

```bash
sudo -u postgres psql <<'SQL'
CREATE USER hustle WITH PASSWORD 'GANTI_PASSWORD_KUAT';
CREATE DATABASE hustle_store OWNER hustle;
SQL
```

---

## 3. Ambil kode dari GitHub

Repo: <https://github.com/rendyirawann/hustle-store>

```bash
git clone https://github.com/rendyirawann/hustle-store.git /var/www/hustlestore
```

Aset Vite (`public/build`) **ikut di-commit**, jadi server **tidak perlu Node.js**.
Konsekuensinya, setiap kali mengubah CSS/JS di laptop, jalankan `npm run build` **sebelum** commit & push.

> Repo ini **publik**. Rahasia hanya boleh ada di `.env` (sudah di-`.gitignore`), jangan pernah ditulis
> di kode. Pertimbangkan mengubah repo menjadi **private** (GitHub → Settings → Danger Zone → Change visibility).
> Kalau repo private, `git clone` di server butuh login: pakai
> [deploy key](https://docs.github.com/en/authentication/connecting-to-github-with-ssh/managing-deploy-keys)
> (SSH) atau personal access token.

---

## 4. File `.env` produksi

```bash
cd /var/www/hustlestore
cp .env.example .env
nano .env
```

Isi/ubah minimal baris berikut:

```env
APP_NAME=HustleStore
APP_ENV=production
APP_DEBUG=false                     # WAJIB false
APP_URL=https://vip.hustlesync.my.id
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432                        # di laptop 5433, di server default 5432
DB_DATABASE=hustle_store
DB_USERNAME=hustle
DB_PASSWORD=GANTI_PASSWORD_KUAT

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true          # cookie hanya via HTTPS
CACHE_STORE=database                # atau redis bila terpasang
QUEUE_CONNECTION=database

# Email (Gmail SMTP + App Password)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=465
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_FROM_ADDRESS=...
ADMIN_ORDER_EMAIL=...

# Midtrans — ganti ke key PRODUKSI saat siap terima pembayaran asli
MIDTRANS_MERCHANT_ID=...
MIDTRANS_CLIENT_KEY=...
MIDTRANS_SERVER_KEY=...
MIDTRANS_IS_PRODUCTION=true

# VIPayment (sinkron daftar harga)
VIPAYMENT_API_ID=...
VIPAYMENT_API_KEY=...
VIPAYMENT_PRICE_TIER=basic          # basic | premium | special sesuai level akun VIP

# SEO / kontak
SEO_WHATSAPP=62xxxxxxxxxx           # nomor WA asli

# Akun admin awal (dipakai db:seed). Kosongkan = password acak ditampilkan saat seed.
SUPERADMIN_EMAIL=emailkamu@gmail.com
SUPERADMIN_PASSWORD=GANTI_PASSWORD_ADMIN
```

> 🔒 `.env` berisi semua rahasia. Jangan pernah di-commit atau dikirim ke orang lain.

---

## 5. Install aplikasi

```bash
cd /var/www/hustlestore
composer install --no-dev --optimize-autoloader
php artisan key:generate --force           # hanya saat install pertama
php artisan migrate --force
php artisan db:seed --force                # permission + superadmin + settings + katalog awal
php artisan storage:link
php artisan vip:sync                       # timpa harga seed dengan harga VIP terbaru
php artisan optimize                       # config + route + view + event cache

# Izin folder untuk web server
chown -R www-data:www-data /var/www/hustlestore
chmod -R 775 storage bootstrap/cache
```

Login admin di `https://vip.hustlesync.my.id/admin/login` memakai `SUPERADMIN_EMAIL` dan `SUPERADMIN_PASSWORD`
dari `.env`. Kalau password dikosongkan, password acak ditampilkan sekali di output `db:seed`.
Setelah login, ganti password di menu profil, lalu hapus `SUPERADMIN_PASSWORD` dari `.env` server.

> **Mau membawa data dari laptop** (logo brand yang sudah di-set, setting, order)? Pakai dump database, bukan seed:
> ```powershell
> # laptop
> pg_dump -h 127.0.0.1 -p 5433 -U postgres -Fc hustle_store -f hustle_store.dump
> scp hustle_store.dump root@76.13.23.58:/tmp/
> ```
> ```bash
> # server, ganti langkah "migrate + db:seed" dengan:
> pg_restore -h 127.0.0.1 -U hustle -d hustle_store --no-owner --no-acl /tmp/hustle_store.dump
> ```

---

## 6. Nginx untuk subdomain

```bash
nano /etc/nginx/sites-available/vip.hustlesync.my.id
```

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name vip.hustlesync.my.id;
    root /var/www/hustlestore/public;
    index index.php;
    charset utf-8;
    client_max_body_size 20M;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    gzip on;
    gzip_types text/css application/javascript application/json image/svg+xml text/plain;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~* \.(?:css|js|jpg|jpeg|png|gif|webp|svg|ico|woff2?)$ {
        expires 30d;
        access_log off;
        try_files $uri =404;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* { deny all; }
}
```

```bash
ln -s /etc/nginx/sites-available/vip.hustlesync.my.id /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx
```

> Kalau di server yang sama sudah ada situs lain (misalnya domain utama `hustlesync.my.id`), aman saja.
> Nginx memisahkan situs berdasarkan `server_name`.

### HTTPS (SSL gratis Let's Encrypt)

Jalankan **setelah** DNS sudah mengarah ke server (langkah 1):

```bash
certbot --nginx -d vip.hustlesync.my.id --redirect -m emailkamu@gmail.com --agree-tos -n
```

Certbot memperpanjang sertifikat otomatis.

---

## 7. Cron (WAJIB, untuk sinkron harga otomatis)

Scheduler Laravel menjalankan `vip:sync` **tiap 30 menit** (lihat `routes/console.php`).
Dengan begitu harga modal, harga jual, dan status stok selalu mengikuti VIPayment.

```bash
crontab -u www-data -e
```

Tambahkan satu baris ini:

```cron
* * * * * cd /var/www/hustlestore && php artisan schedule:run >> /dev/null 2>&1
```

Cek: `php artisan schedule:list`. Log tiap sinkron ada di `storage/logs/vip-sync.log`.

---

## 8. Pengaturan di VIPayment (Profile → Pengaturan API)

| Field | Isi |
|---|---|
| **Whitelist IP** | `103.138.218.83,76.13.23.58`, yaitu IP laptop/rumah ditambah IP server, dipisah koma |
| **Callback URL** | **Kosongkan** (penjelasan di bawah) |
| **API Status** | Biarkan **Development** |

Pastikan IP keluar server memang `76.13.23.58` (di VPS biasanya sama dengan A record):

```bash
curl -4 ifconfig.me
```

Lalu tes dari server: `php artisan vip:sync --dry-run`. Kalau muncul `IP x.x.x.x is not permitted`,
masukkan IP tersebut ke Whitelist IP.

**Kenapa Callback URL dikosongkan?** Callback dipakai VIPayment untuk mengabari status pesanan yang dibuat
**lewat API** (`type=order`). HustleStore hanya membaca daftar harga, sedangkan top up tetap dilakukan admin
secara manual di web VIPayment. Jadi tidak ada pesanan API yang perlu dilaporkan balik.
Karena alasan yang sama, **API Status tidak perlu Production**. Daftar layanan sudah keluar
dengan harga asli dalam mode Development.

> Kalau nanti top up mau dibuat otomatis lewat API, barulah perlu: endpoint callback
> (misalnya `https://vip.hustlesync.my.id/api/vipayment-callback`) yang memverifikasi header
> `X-Client-Signature` = md5(API ID + API KEY) dan hanya menerima IP VIPayment `178.248.73.218`,
> lalu API Status diubah ke Production.

IP rumah (`103.138.218.83`) bisa berubah kalau ISP memakai IP dinamis. Itu hanya memengaruhi sinkron dari laptop, tidak memengaruhi server.

---

## 9. Midtrans

Dashboard Midtrans → **Settings → Configuration**:

```
Payment Notification URL:  https://vip.hustlesync.my.id/api/midtrans-webhook
```

Saat siap menerima uang asli: ganti key di `.env` ke key **Production**, `MIDTRANS_IS_PRODUCTION=true`,
lalu `php artisan optimize`.

---

## 10. Update / deploy ulang

```bash
cd /var/www/hustlestore
sudo -u www-data git pull                  # atau: git pull lalu chown ulang
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear && php artisan optimize
chown -R www-data:www-data storage bootstrap/cache
systemctl reload php8.3-fpm                # wajib bila opcache.validate_timestamps=0
```

> Route sudah **bebas closure**, jadi `route:cache` pasti berhasil. Jangan menambah route berbentuk closure.
> Setiap kali mengubah `.env`, jalankan ulang `php artisan optimize`.

---

## 11. Performa (disarankan)

**OPcache**: edit `/etc/php/8.3/fpm/conf.d/10-opcache.ini`, lalu `systemctl restart php8.3-fpm`:

```ini
opcache.enable=1
opcache.memory_consumption=192
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0   ; ingat reload php-fpm tiap deploy
```

Yang sudah tertanam di aplikasi:
- Query landing di-cache 30 menit (`landing.brands.v1`) dan **otomatis di-refresh** setiap produk/brand berubah,
  termasuk saat `vip:sync` mengubah harga.
- Gambar sudah dioptimasi, hero di-`preload`. Settings di-cache (`settings.all`).

Opsional:
- **Redis** untuk `CACHE_STORE` & `SESSION_DRIVER` (`apt install redis-server`).
- **Queue worker** (Supervisor + `php artisan queue:work`) kalau nanti email dibuat queued.
  Saat ini email masih dikirim langsung, jadi worker belum wajib.

---

## 12. Cek akhir

- [ ] `https://vip.hustlesync.my.id` terbuka dengan gembok SSL
- [ ] Login admin berhasil dan password superadmin sudah diganti
- [ ] `php artisan vip:sync --dry-run` di server sukses (bukan "IP not permitted")
- [ ] `php artisan schedule:list` menampilkan `vip:sync` dan cron sudah terpasang
- [ ] Notification URL Midtrans mengarah ke subdomain
- [ ] Order percobaan: bayar, status jadi paid, email masuk ke admin & customer
- [ ] `SEO_WHATSAPP` sudah nomor asli

## Troubleshooting

| Gejala | Penyebab & solusi |
|---|---|
| **502 Bad Gateway** | PHP-FPM mati atau path socket salah. Cek `systemctl status php8.3-fpm` dan pastikan `fastcgi_pass` sesuai versi PHP. |
| **500 Server Error** | Lihat `storage/logs/laravel.log`. Paling sering masalah izin folder: ulangi `chown`/`chmod` di langkah 5. |
| CSS/JS tidak muncul / versi lama | Lupa `npm run build` sebelum push. Build di laptop, commit `public/build`, push, lalu `git pull` di server. |
| `git pull`: *dubious ownership* | `git config --global --add safe.directory /var/www/hustlestore` |
| Link masih `http://` atau `127.0.0.1` | `APP_URL` salah. Perbaiki lalu `php artisan optimize`. |
| `IP ... is not permitted` | Tambahkan IP itu ke Whitelist IP VIPayment (langkah 8). |
| Harga tidak ter-update | Cron belum terpasang. Cek `crontab -u www-data -l` dan `storage/logs/vip-sync.log`. |
| Email tidak terkirim | Beberapa penyedia VPS memblokir port SMTP 465/587. Tanyakan ke provider atau pakai layanan email transaksional. |
