# Fix Films in Production

## Problem
Film posters dan trailers tidak muncul di production (menggunakan placeholder).

## Root Cause
Database di-reset atau seeder tidak berjalan dengan benar saat deployment.

## Solution

### Automatic Fix (via Deployment)
Script akan otomatis berjalan saat deployment:
1. `verify-films.sh` - Cek status film
2. `fix-films-production.sh` - Fix jika diperlukan

### Manual Fix (jika diperlukan)

#### Option 1: Run Fix Script
```bash
# SSH ke server
ssh -i maycloudaws.pem ubuntu@54.254.162.155

# Masuk ke directory
cd ~/ujikom-backend-v2

# Run fix script
docker exec backend chmod +x fix-films-production.sh
docker exec backend ./fix-films-production.sh

# Verify
docker exec backend ./verify-films.sh
```

#### Option 2: Re-seed Films Only
```bash
# SSH ke server
ssh -i maycloudaws.pem ubuntu@54.254.162.155

# Re-seed films
docker exec backend php artisan db:seed --class=FilmSeeder --force

# Verify
docker exec backend php artisan tinker --execute="
  \$films = \App\Models\Film::all();
  foreach (\$films as \$film) {
    echo \$film->title . ': ' . \$film->poster . PHP_EOL;
  }
"
```

#### Option 3: Manual Update via Tinker
```bash
docker exec -it backend php artisan tinker

# Then run:
$film = \App\Models\Film::where('title', 'Avengers: Endgame')->first();
$film->update([
    'poster' => 'https://m.media-amazon.com/images/M/MV5BMTc5MDE2ODcwNV5BMl5BanBnXkFtZTgwMzI2NzQ2NzM@._V1_SX300.jpg',
    'trailer' => 'https://www.youtube.com/watch?v=TcMBFSGVi1c'
]);
```

## Verification

### Check Films Data
```bash
docker exec backend ./verify-films.sh
```

### Check via API
```bash
curl https://api.absolutecinema.my.id/api/films | jq '.data[0].poster'
```

### Check in Browser
Visit: https://absolutecinema.my.id/customer/films

## Prevention

1. **FilmSeeder.php** sudah memiliki URL poster dan trailer yang benar
2. **Deployment workflow** otomatis verify dan fix
3. **Scripts tersedia** untuk manual fix jika diperlukan

## Film Data Reference

```php
[
    'Avengers: Endgame' => [
        'poster' => 'https://m.media-amazon.com/images/M/MV5BMTc5MDE2ODcwNV5BMl5BanBnXkFtZTgwMzI2NzQ2NzM@._V1_SX300.jpg',
        'trailer' => 'https://www.youtube.com/watch?v=TcMBFSGVi1c'
    ],
    'Spider-Man: No Way Home' => [
        'poster' => 'https://m.media-amazon.com/images/M/MV5BZWMyYzFjYTYtNTRjYi00OGExLWE2YzgtOGRmYjAxZTU3NzBiXkEyXkFqcGdeQXVyMzQ0MzA0NTM@._V1_SX300.jpg',
        'trailer' => 'https://www.youtube.com/watch?v=JfVOs4VSpmA'
    ],
    'The Batman' => [
        'poster' => 'https://m.media-amazon.com/images/M/MV5BMDdmMTBiNTYtMDIzNi00NGVlLWIzMDYtZTk3MTQ3NGQxZGEwXkEyXkFqcGdeQXVyMzMwOTU5MDk@._V1_SX300.jpg',
        'trailer' => 'https://www.youtube.com/watch?v=mqqft2x_Aa4'
    ],
    'Top Gun: Maverick' => [
        'poster' => 'https://m.media-amazon.com/images/M/MV5BZWYzOGEwNTgtNWU3NS00ZTQ0LWJkODUtMmVhMjIwMjA1ZmQwXkEyXkFqcGdeQXVyMjkwOTAyMDU@._V1_SX300.jpg',
        'trailer' => 'https://www.youtube.com/watch?v=giXco2jaZ_4'
    ],
    'Oppenheimer' => [
        'poster' => 'https://m.media-amazon.com/images/M/MV5BMDBmYTZjNjUtN2M1MS00MTQ2LTk2ODgtNzc2M2QyZGE5NTVjXkEyXkFqcGdeQXVyNzAwMjU2MTY@._V1_SX300.jpg',
        'trailer' => 'https://www.youtube.com/watch?v=uYPbbksJxIg'
    ],
    'Barbie' => [
        'poster' => 'https://m.media-amazon.com/images/M/MV5BNjU3N2QxNzYtMjk1NC00MTc4LTk1NTQtMmUxNTljM2I0NDA5XkEyXkFqcGdeQXVyODE5NzE3OTE@._V1_SX300.jpg',
        'trailer' => 'https://www.youtube.com/watch?v=pBk4NYhWNMM'
    ]
]
```

## Notes

- Semua URL poster menggunakan IMDb (m.media-amazon.com) - reliable dan permanent
- Semua URL trailer menggunakan YouTube - public dan accessible
- Scripts sudah executable dan siap digunakan
- Deployment workflow otomatis handle verification dan fixing
