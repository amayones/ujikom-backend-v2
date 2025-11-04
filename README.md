# Backend AC - Absolute Cinema API

Backend Laravel 12 untuk aplikasi Absolute Cinema yang menyediakan API lengkap untuk sistem bioskop.

## 🚀 Fitur Utama

- **Autentikasi**: Laravel Sanctum dengan role-based access
- **Multi Role**: Customer, Admin, Owner, Cashier
- **API Lengkap**: CRUD untuk semua entitas
- **Payment Integration**: Simulasi Midtrans callback
- **PDF Export**: Laporan keuangan untuk Owner
- **Offline Order**: Pemesanan langsung oleh Kasir

## 📋 Instalasi

1. Clone dan setup:
```bash
cd backend-ac
composer install
cp .env.example .env
php artisan key:generate
```

2. Setup database:
```bash
php artisan migrate --seed
```

3. Jalankan server:
```bash
php artisan serve
```

## 🔐 Akun Default

| Role | Email | Password |
|------|-------|----------|
| Customer | customer@test.com | password123 |
| Admin | admin@test.com | admin123 |
| Owner | owner@test.com | owner123 |
| Kasir | kasir@test.com | kasir123 |

## 🌐 API Endpoints

### Auth
- `POST /api/register` - Registrasi user
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user
- `GET /api/profile` - Get profile
- `PUT /api/profile` - Update profile

### Customer
- `GET /api/films` - Daftar film
- `GET /api/films/{id}` - Detail film
- `GET /api/schedules/{film_id}` - Jadwal film
- `GET /api/seats/{schedule_id}` - Layout kursi
- `POST /api/checkout` - Checkout tiket
- `GET /api/orders` - Riwayat pesanan
- `GET /api/orders/{id}` - Detail pesanan

### Admin
- `GET|POST|PUT|DELETE /api/admin/films` - CRUD Film
- `GET|POST|PUT|DELETE /api/admin/users` - CRUD User
- `GET|POST|PUT|DELETE /api/admin/schedules` - CRUD Jadwal
- `GET|POST|PUT|DELETE /api/admin/prices` - CRUD Harga
- `GET|POST|PUT|DELETE /api/admin/seats` - CRUD Kursi

### Owner
- `GET /api/owner/reports` - Laporan keuangan
- `GET /api/owner/reports/export-pdf` - Export PDF

### Kasir
- `POST /api/cashier/offline-order` - Pesanan offline
- `GET /api/cashier/process-online/{order_id}` - Proses pesanan online
- `POST /api/cashier/print-ticket/{order_id}` - Print tiket

### Payment
- `POST /api/payment/callback` - Callback Midtrans

## 📊 Database Schema

### Users
- id, name, email, password, role, phone, timestamps, soft_deletes

### Films
- id, title, genre, duration, status, description, poster, base_price, timestamps, soft_deletes

### Studios
- id, name, capacity, type, timestamps

### Schedules
- id, film_id, studio_id, show_time, timestamps

### Seats
- id, studio_id, row, column, category, status, timestamps

### Prices
- id, day_type, seat_category, price, timestamps

### Orders
- id, order_number, user_id, schedule_id, total_amount, payment_status, order_type, timestamps

### Order Items
- id, order_id, seat_id, price, timestamps

### Reports
- id, report_date, type, amount, description, timestamps

## 🔧 Konfigurasi

### CORS
Sudah dikonfigurasi untuk menerima request dari frontend React.

### Sanctum
Token-based authentication dengan expiration sesuai kebutuhan.

### PDF Export
Menggunakan `barryvdh/laravel-dompdf` untuk generate laporan PDF.

## 🧪 Testing

Gunakan Postman atau tools API testing lainnya:

1. Login untuk mendapatkan token
2. Gunakan token di header: `Authorization: Bearer {token}`
3. Test semua endpoint sesuai role

## 📝 Response Format

Semua API menggunakan format response yang konsisten:

```json
{
  "success": true,
  "message": "Success message",
  "data": { ... }
}
```

## 🚀 Deployment

1. Setup production environment
2. Configure database
3. Run migrations and seeders
4. Setup web server (Apache/Nginx)
5. Configure SSL certificate

## 📞 Support

Untuk pertanyaan atau issue, silakan hubungi tim development.