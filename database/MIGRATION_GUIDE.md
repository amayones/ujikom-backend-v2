# Migration Guide - Absolute Cinema Backend

## Overview
Migration files telah dikonsolidasikan menjadi struktur yang sempurna dan final. Semua perubahan incremental telah digabungkan ke dalam migration utama.

## Migration Files (Final)

### 1. Core Laravel Tables
- `0001_01_01_000000_create_users_table.php` - Users, password resets, sessions
- `0001_01_01_000001_create_cache_table.php` - Cache system
- `0001_01_01_000002_create_jobs_table.php` - Queue jobs
- `2025_10_30_090807_create_personal_access_tokens_table.php` - API tokens (Sanctum)

### 2. Cinema Business Tables
- `2024_01_01_000002_create_studios_table.php` - Studio/theater rooms
- `2024_01_01_000003_create_films_table.php` - Film catalog
- `2024_01_01_000004_create_schedules_table.php` - Film schedules
- `2024_01_01_000005_create_seats_table.php` - Seat inventory
- `2024_01_01_000006_create_prices_table.php` - Pricing rules
- `2024_01_01_000007_create_orders_table.php` - Customer orders
- `2024_01_01_000008_create_order_items_table.php` - Order line items

## Database Schema

### Users Table
```sql
- id (PK)
- name
- email (unique)
- role (enum: customer, admin, owner, cashier)
- phone (nullable)
- email_verified_at (nullable)
- password
- remember_token
- deleted_at (soft delete)
- timestamps
```

### Studios Table
```sql
- id (PK)
- name
- capacity
- timestamps
```

### Films Table
```sql
- id (PK)
- title
- genre
- duration (minutes)
- status (enum: play_now, coming_soon)
- description
- poster (nullable)
- base_price
- deleted_at (soft delete)
- timestamps
```

### Schedules Table
```sql
- id (PK)
- film_id (FK -> films)
- studio_id (FK -> studios)
- show_time (datetime)
- base_price
- day_type (enum: weekday, weekend)
- timestamps
- indexes: show_time, (studio_id, show_time)
```

### Seats Table
```sql
- id (PK)
- studio_id (FK -> studios)
- row
- column
- status (enum: available, maintenance)
- timestamps
- index: (studio_id, row, column)
```

### Prices Table
```sql
- id (PK)
- day_type (enum: weekday, weekend)
- price
- timestamps
```

### Orders Table
```sql
- id (PK)
- order_number (unique)
- user_id (FK -> users)
- schedule_id (FK -> schedules)
- total_amount
- payment_status (enum: pending, paid, failed, cancelled)
- ticket_status (enum: unused, scanned)
- order_type (default: online)
- customer_name (nullable)
- customer_phone (nullable)
- scanned_at (nullable)
- scanned_by (FK -> users, nullable)
- timestamps
- indexes: payment_status, order_number, created_at
```

### Order Items Table
```sql
- id (PK)
- order_id (FK -> orders)
- seat_id (FK -> seats)
- price
- timestamps
- index: seat_id
```

## Changes Made

### Consolidated Changes
1. **Users table**: Added role, phone, soft deletes from the start
2. **Studios table**: Removed unused 'type' field
3. **Schedules table**: Added base_price and day_type from the start, added indexes
4. **Seats table**: Removed 'category' field, added composite index
5. **Prices table**: Removed 'seat_category' field
6. **Orders table**: Added ticket_status, customer info, scanned fields, and indexes from the start
7. **Order Items table**: Added seat_id index

### Removed Migration Files
- `2024_01_01_000001_modify_users_table.php` (merged into users table)
- `2024_01_01_000009_create_reports_table.php` (not used)
- `2024_12_01_000001_add_base_price_to_schedules_table.php` (merged)
- `2024_12_01_000002_add_indexes_for_performance.php` (merged)
- `2024_12_01_000003_drop_reports_table.php` (not needed)
- `2024_12_01_000005_add_customer_info_to_orders.php` (merged)
- `2025_11_14_120827_add_ticket_status_to_orders_table.php` (merged)
- `2025_11_15_161949_remove_category_from_seats_and_prices.php` (merged)

## Running Migrations

### Fresh Install
```bash
php artisan migrate:fresh --seed
```

### Production Deploy
```bash
php artisan migrate --force
```

## Backup
Old migration files are backed up in `database/migrations_backup/` directory.

## Notes
- All foreign keys have proper cascade/set null actions
- Indexes added for performance optimization
- Soft deletes enabled for users and films
- Enum fields used for status/type columns
- All migrations tested and working correctly
