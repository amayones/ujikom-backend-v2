# Quick Fix Commands - Production Seeding

## 🚀 Fastest Fix (Copy-Paste Ready)

### SSH ke Server
```bash
ssh ubuntu@be-ac.amayones.my.id
cd ~/ujikom-backend-v2
```

### Fix Seeding (Method 1 - RECOMMENDED)
```bash
docker exec backend chmod +x deploy-seed-fix.sh && docker exec backend ./deploy-seed-fix.sh
```

### Fix Seeding (Method 2 - One-liner)
```bash
docker exec backend bash -c "php artisan migrate:fresh --force && sleep 3 && php artisan db:seed --force"
```

### Fix Seeding (Method 3 - Per Seeder)
```bash
docker exec backend php artisan migrate:fresh --force
docker exec backend php artisan db:seed --class=UserSeeder --force
docker exec backend php artisan db:seed --class=StudioSeeder --force
docker exec backend php artisan db:seed --class=FilmSeeder --force
docker exec backend php artisan db:seed --class=SeatSeeder --force
docker exec backend php artisan db:seed --class=PriceSeeder --force
docker exec backend php artisan db:seed --class=ScheduleSeeder --force
docker exec backend php artisan db:seed --class=OrderSeeder --force
```

## ✅ Verify Data
```bash
docker exec backend php artisan tinker --execute="echo 'Users: '.\App\Models\User::count().', Films: '.\App\Models\Film::count().', Studios: '.\App\Models\Studio::count();"
```

## 🔄 Restart Container with Seeding
```bash
docker stop backend && docker rm backend
docker run -d -p 80:80 -p 443:443 -v /etc/letsencrypt:/etc/letsencrypt:ro -v /var/www/certbot:/var/www/certbot -e RUN_MIGRATIONS=true --name backend --env-file .env cinema-backend
sleep 15
docker exec backend ./deploy-seed-fix.sh
```

## 📊 Check Status
```bash
# Container status
docker ps | grep backend

# Logs
docker logs backend --tail 50

# Laravel logs
docker exec backend tail -20 storage/logs/laravel.log

# Database connection
docker exec backend php artisan db:show
```

## 🧹 Clear Caches
```bash
docker exec backend php artisan config:clear
docker exec backend php artisan cache:clear
docker exec backend php artisan route:clear
docker exec backend php artisan view:clear
```

## 🔧 Debug Commands
```bash
# Check environment
docker exec backend php artisan config:show database

# Test database
docker exec backend php artisan tinker --execute="DB::connection()->getPdo();"

# Count all tables
docker exec backend php artisan tinker --execute="
  echo 'Users: '.\App\Models\User::count().PHP_EOL;
  echo 'Studios: '.\App\Models\Studio::count().PHP_EOL;
  echo 'Films: '.\App\Models\Film::count().PHP_EOL;
  echo 'Seats: '.\App\Models\Seat::count().PHP_EOL;
  echo 'Prices: '.\App\Models\Price::count().PHP_EOL;
  echo 'Schedules: '.\App\Models\Schedule::count().PHP_EOL;
  echo 'Orders: '.\App\Models\Order::count().PHP_EOL;
"
```

## 📝 Expected Data Count
- Users: 4 (customer, admin, owner, cashier)
- Studios: 3 (Studio 1, 2, 3)
- Films: 6 (3 play_now, 3 coming_soon)
- Seats: 150 (50 per studio, 5 rows x 10 columns)
- Prices: 2 (weekday, weekend)
- Schedules: 42+ (depends on films)
- Orders: varies

## 🎯 Test API Endpoints
```bash
# Test films endpoint
curl https://be-ac.amayones.my.id/api/films

# Test login
curl -X POST https://be-ac.amayones.my.id/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"customer@test.com","password":"password"}'
```

## 🔐 Test Users
```
Email: customer@test.com | Password: password | Role: customer
Email: admin@test.com    | Password: password | Role: admin
Email: owner@test.com    | Password: password | Role: owner
Email: cashier@test.com  | Password: password | Role: cashier
```

## 🚨 Emergency Full Reset
```bash
cd ~/ujikom-backend-v2
git pull origin main
docker stop backend && docker rm backend
docker build -t cinema-backend .
docker run -d -p 80:80 -p 443:443 -v /etc/letsencrypt:/etc/letsencrypt:ro -v /var/www/certbot:/var/www/certbot -e RUN_MIGRATIONS=true --name backend --env-file .env cinema-backend
sleep 20
docker exec backend chmod +x deploy-seed-fix.sh
docker exec backend ./deploy-seed-fix.sh
docker exec backend php artisan config:clear
docker exec backend php artisan cache:clear
```

## 📱 Frontend Check
Setelah seeding berhasil, test di frontend:
1. Buka aplikasi frontend
2. Cek halaman Films - harus ada 6 films
3. Test login dengan user di atas
4. Cek schedule - harus ada jadwal film

## 💡 Tips
- Selalu tunggu 10-15 detik setelah start container
- Jika gagal, coba 2-3x lagi (database mungkin belum ready)
- Cek logs jika masih gagal
- Pastikan .env production sudah benar
