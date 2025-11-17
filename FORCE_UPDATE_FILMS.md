# Force Update Films di Production

## Masalah
Films di production menggunakan placeholder images, bukan poster asli dari IMDb seperti di localhost.

## Penyebab
Database production masih menggunakan data lama sebelum FilmSeeder diupdate dengan IMDb posters.

## Solusi

### Opsi 1: Via Script (RECOMMENDED)
```bash
# SSH ke server
ssh ubuntu@be-ac.amayones.my.id
cd ~/ujikom-backend-v2

# Jalankan update script
docker exec backend chmod +x update-films-production.sh
docker exec backend ./update-films-production.sh
```

### Opsi 2: Manual via Tinker
```bash
docker exec backend php artisan tinker --execute="
    DB::table('order_items')->delete();
    DB::table('orders')->delete();
    DB::table('schedules')->delete();
    DB::table('films')->delete();
"

docker exec backend php artisan db:seed --class=FilmSeeder --force
docker exec backend php artisan db:seed --class=ScheduleSeeder --force
```

### Opsi 3: Full Reseed
```bash
docker exec backend ./deploy-seed-fix.sh
```

## Verifikasi

```bash
# Cek via API
curl https://be-ac.amayones.my.id/api/films | jq '.data.play_now[0].poster'

# Expected: URL IMDb (m.media-amazon.com)
# Wrong: placeholder URL (via.placeholder.com)
```

## Expected Posters

1. **Avengers: Endgame**
   - `https://m.media-amazon.com/images/M/MV5BMTc5MDE2ODcwNV5BMl5BanBnXkFtZTgwMzI2NzQ2NzM@._V1_SX300.jpg`

2. **Spider-Man: No Way Home**
   - `https://m.media-amazon.com/images/M/MV5BZWMyYzFjYTYtNTRjYi00OGExLWE2YzgtOGRmYjAxZTU3NzBiXkEyXkFqcGdeQXVyMzQ0MzA0NTM@._V1_SX300.jpg`

3. **The Batman**
   - `https://m.media-amazon.com/images/M/MV5BMDdmMTBiNTYtMDIzNi00NGVlLWIzMDYtZTk3MTQ3NGQxZGEwXkEyXkFqcGdeQXVyMzMwOTU5MDk@._V1_SX300.jpg`

4. **Top Gun: Maverick**
   - `https://m.media-amazon.com/images/M/MV5BZWYzOGEwNTgtNWU3NS00ZTQ0LWJkODUtMmVhMjIwMjA1ZmQwXkEyXkFqcGdeQXVyMjkwOTAyMDU@._V1_SX300.jpg`

5. **Oppenheimer**
   - `https://m.media-amazon.com/images/M/MV5BMDBmYTZjNjUtN2M1MS00MTQ2LTk2ODgtNzc2M2QyZGE5NTVjXkEyXkFqcGdeQXVyNzAwMjU2MTY@._V1_SX300.jpg`

6. **Barbie**
   - `https://m.media-amazon.com/images/M/MV5BNjU3N2QxNzYtMjk1NC00MTc4LTk1NTQtMmUxNTljM2I0NDA5XkEyXkFqcGdeQXVyODE5NzE3OTE@._V1_SX300.jpg`
