# Postman Collection Guide - Absolute Cinema API

## Import Collection

1. Buka Postman
2. Import file: `postman_collection.json`
3. Collection akan muncul dengan nama "Absolute Cinema API"

---

## Environment Variables

Collection sudah include 2 variables:

```
base_url = https://be-ac.amayones.my.id/api
token = (auto-filled after login)
```

---

## Test Accounts (Seeder Data)

### 1. Customer Account
```
Email: customer@test.com
Password: password
Role: customer
```

### 2. Admin Account
```
Email: admin@test.com
Password: password
Role: admin
```

### 3. Owner Account
```
Email: owner@test.com
Password: password
Role: owner
```

### 4. Cashier Account
```
Email: cashier@test.com
Password: password
Role: cashier
```

---

## Testing Flow

### Step 1: Login
1. Go to: `1. Authentication` → `Login`
2. Body sudah terisi dengan `customer@test.com` dan `password`
3. Click **Send**
4. Token akan otomatis tersimpan di collection variable

### Step 2: Test Public Endpoints (No Auth)
- `2. Public` → `Get All Films`
- `2. Public` → `Get Film Detail` (ID: 1-6)
- `2. Public` → `Get Schedules by Film` (film_id: 1-6)
- `2. Public` → `Get Seats by Schedule` (schedule_id: 1+)
- `2. Public` → `Get All Studios`

### Step 3: Test Customer Endpoints (Auth Required)
Login sebagai customer terlebih dahulu, lalu:
- `3. Customer` → `Checkout`
- `3. Customer` → `Get My Orders`
- `3. Customer` → `Get Order Detail`
- `3. Customer` → `Cancel Order`

### Step 4: Test Admin Endpoints (Auth Required)
Login sebagai admin (`admin@test.com`), lalu:
- `4. Admin - Films` → CRUD operations
- `5. Admin - Users` → CRUD operations
- `6. Admin - Schedules` → CRUD operations
- `7. Admin - Prices` → CRUD operations
- `8. Admin - Seats` → CRUD operations

### Step 5: Test Owner Endpoints (Auth Required)
Login sebagai owner (`owner@test.com`), lalu:
- `9. Owner - Reports` → Get Reports
- `9. Owner - Reports` → Export PDF Report

### Step 6: Test Cashier Endpoints (Auth Required)
Login sebagai cashier (`cashier@test.com`), lalu:
- `10. Cashier` → Create Offline Order
- `10. Cashier` → Create Online Order
- `10. Cashier` → Process Online Order
- `10. Cashier` → Print Ticket

### Step 7: Test Payment Endpoints
- `11. Payment` → Get Midtrans Client Key (no auth)
- `11. Payment` → Get Snap Token (auth required)
- `11. Payment` → Check Payment Status (auth required)
- `11. Payment` → Midtrans Callback (webhook, no auth)

---

## Seeder Data Reference

### Films (6 movies)
1. Deadpool & Wolverine - Action, Comedy - 128 min - Rp 55,000 - play_now
2. Inside Out 2 - Animation, Family - 96 min - Rp 45,000 - play_now
3. Dune: Part Two - Sci-Fi, Adventure - 166 min - Rp 52,000 - play_now
4. Wicked - Fantasy, Musical - 160 min - Rp 50,000 - coming_soon
5. Moana 2 - Animation, Adventure - 100 min - Rp 48,000 - coming_soon
6. Gladiator II - Action, Drama - 148 min - Rp 55,000 - coming_soon

### Prices (4 combinations)
1. Weekday + Regular = Rp 35,000
2. Weekday + VIP = Rp 50,000
3. Weekend + Regular = Rp 45,000
4. Weekend + VIP = Rp 65,000

### Studios
- Studio 1: Regular (30 seats)
- Studio 2: VIP (20 seats)
- Studio 3: IMAX (40 seats)

---

## Common Issues

### 1. "Unauthenticated" Error
**Solution:** Login terlebih dahulu, token akan otomatis tersimpan

### 2. "Unauthorized" Error (403)
**Solution:** Login dengan akun yang sesuai role:
- Admin endpoints → login sebagai admin
- Owner endpoints → login sebagai owner
- Cashier endpoints → login sebagai cashier

### 3. "Not Found" Error (404)
**Solution:** Pastikan ID yang digunakan ada di database (cek seeder data)

### 4. "Validation Error" (422)
**Solution:** Cek request body, pastikan semua field required terisi dengan benar

---

## API Endpoints Summary

**Total: 49 Endpoints**

- Public: 5 endpoints
- Authentication: 5 endpoints
- Customer: 4 endpoints
- Admin Films: 5 endpoints
- Admin Users: 5 endpoints
- Admin Schedules: 5 endpoints
- Admin Prices: 5 endpoints
- Admin Seats: 5 endpoints
- Owner Reports: 2 endpoints
- Cashier: 4 endpoints
- Payment: 4 endpoints

---

## Notes

- Semua password di seeder adalah: `password`
- Token akan expire setelah beberapa waktu, login ulang jika diperlukan
- Untuk testing, gunakan data dari seeder
- Jangan hapus data seeder untuk testing yang konsisten
