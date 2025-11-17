# Seeding Troubleshooting Guide

## Masalah: Data tidak ter-seed di production

### Penyebab Umum:
1. Database belum siap saat seeding dijalankan
2. Koneksi database timeout
3. Foreign key constraints error
4. Memory limit PHP terlalu kecil
5. Timeout pada proses seeding

### Solusi Otomatis (Sudah Diimplementasi):

#### 1. Deploy Workflow dengan Retry Mechanism
- Workflow akan otomatis retry jika seeding gagal
- Seeder dijalankan satu per satu untuk isolasi error
- Verifikasi data setelah seeding

#### 2. Docker Entrypoint
- Menunggu database siap sebelum seeding
- Retry otomatis jika gagal

#### 3. Seeding Script
- `seed-production.sh` untuk Linux/Docker
- `seed-production.bat` untuk Windows

### Manual Fix (Jika Otomatis Gagal):

#### Opsi 1: Via Docker Exec
```bash
# SSH ke server
ssh user@be-ac.amayones.my.id

# Masuk ke direktori
cd ~/ujikom-backend-v2

# Jalankan seeding manual
docker exec backend php artisan migrate:fresh --force
docker exec backend php artisan db:seed --force

# Atau jalankan per seeder
docker exec backend php artisan db:seed --class=UserSeeder --force
docker exec backend php artisan db:seed --class=StudioSeeder --force
docker exec backend php artisan db:seed --class=FilmSeeder --force
docker exec backend php artisan db:seed --class=SeatSeeder --force
docker exec backend php artisan db:seed --class=PriceSeeder --force
docker exec backend php artisan db:seed --class=ScheduleSeeder --force
docker exec backend php artisan db:seed --class=OrderSeeder --force
```

#### Opsi 2: Via Script
```bash
# Jalankan script seeding
docker exec backend chmod +x seed-production.sh
docker exec backend ./seed-production.sh
```

#### Opsi 3: Restart Container dengan Seeding
```bash
# Stop container
docker stop backend
docker rm backend

# Start dengan flag RUN_MIGRATIONS
docker run -d \
  -p 80:80 \
  -p 443:443 \
  -v /etc/letsencrypt:/etc/letsencrypt:ro \
  -v /var/www/certbot:/var/www/certbot \
  -e RUN_MIGRATIONS=true \
  --name backend \
  --env-file .env \
  cinema-backend
```

### Verifikasi Data:

```bash
# Cek jumlah data
docker exec backend php artisan tinker --execute="
  echo 'Users: ' . \App\Models\User::count() . PHP_EOL;
  echo 'Studios: ' . \App\Models\Studio::count() . PHP_EOL;
  echo 'Films: ' . \App\Models\Film::count() . PHP_EOL;
  echo 'Seats: ' . \App\Models\Seat::count() . PHP_EOL;
  echo 'Prices: ' . \App\Models\Price::count() . PHP_EOL;
  echo 'Schedules: ' . \App\Models\Schedule::count() . PHP_EOL;
  echo 'Orders: ' . \App\Models\Order::count() . PHP_EOL;
"

# Expected output:
# Users: 4
# Studios: 3
# Films: 6
# Seats: 150 (50 per studio)
# Prices: varies
# Schedules: varies
# Orders: varies
```

### Debug Logs:

```bash
# Lihat logs container
docker logs backend

# Lihat logs real-time
docker logs -f backend

# Lihat Laravel logs
docker exec backend tail -f storage/logs/laravel.log
```

### Environment Variables Penting:

Pastikan di `.env` production:
```env
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-database
DB_USERNAME=your-username
DB_PASSWORD=your-password

APP_ENV=production
APP_DEBUG=false
```

### Quick Fix Command (All-in-One):

```bash
docker exec backend bash -c "
  php artisan migrate:fresh --force && \
  sleep 2 && \
  php artisan db:seed --class=UserSeeder --force && \
  php artisan db:seed --class=StudioSeeder --force && \
  php artisan db:seed --class=FilmSeeder --force && \
  php artisan db:seed --class=SeatSeeder --force && \
  php artisan db:seed --class=PriceSeeder --force && \
  php artisan db:seed --class=ScheduleSeeder --force && \
  php artisan db:seed --class=OrderSeeder --force && \
  php artisan config:clear && \
  php artisan cache:clear
"
```
