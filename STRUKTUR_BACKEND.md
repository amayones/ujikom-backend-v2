# Struktur Backend - Absolute Cinema

**Tech Stack:** Laravel 12 | PHP 8.2 | SQLite | Sanctum | Midtrans | DomPDF

```
app/
├── Http/Controllers/Admin/     → FilmController, PriceController, ScheduleController, SeatController, UserController
├── Http/Controllers/Auth/      → AuthController
├── Http/Controllers/Cashier/   → OrderController
├── Http/Controllers/Customer/  → OrderController
├── Http/Controllers/Owner/     → ReportController
├── Http/Controllers/           → FilmController, PaymentController, ScheduleController, SeatController
├── Http/Middleware/            → RoleMiddleware
├── Models/                     → Film, Order, OrderItem, Price, Schedule, Seat, Studio, User
└── Helpers/                    → OrderHelper

database/
├── migrations/                 → 9 tables (users, studios, films, schedules, seats, prices, orders, order_items, tokens)
└── seeders/                    → Film, Price, Schedule, Seat, Studio, User

config/                         → midtrans.php, sanctum.php
routes/                         → api.php
resources/views/pdf/            → report.blade.php
.github/workflows/              → deploy.yml
```
