<?php

namespace Database\Seeders;

use App\Models\Discount;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        $discounts = [
            [
                'code' => 'WELCOME50',
                'name' => 'Diskon Selamat Datang 50%',
                'description' => 'Diskon 50% untuk pelanggan baru',
                'type' => 'percentage',
                'value' => 50,
                'max_uses' => 100,
                'used_count' => 0,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(3),
                'is_active' => true
            ],
            [
                'code' => 'WEEKEND20',
                'name' => 'Diskon Weekend 20%',
                'description' => 'Diskon 20% untuk pembelian di akhir pekan',
                'type' => 'percentage',
                'value' => 20,
                'max_uses' => null,
                'used_count' => 0,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonths(6),
                'is_active' => true
            ],
            [
                'code' => 'PROMO10K',
                'name' => 'Potongan 10 Ribu',
                'description' => 'Potongan langsung Rp 10.000',
                'type' => 'fixed',
                'value' => 10000,
                'max_uses' => 50,
                'used_count' => 0,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addMonth(),
                'is_active' => true
            ],
            [
                'code' => 'STUDENT15',
                'name' => 'Diskon Pelajar 15%',
                'description' => 'Diskon khusus untuk pelajar dan mahasiswa',
                'type' => 'percentage',
                'value' => 15,
                'max_uses' => 200,
                'used_count' => 0,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addYear(),
                'is_active' => true
            ],
            [
                'code' => 'FLASH25',
                'name' => 'Flash Sale 25%',
                'description' => 'Diskon flash sale terbatas',
                'type' => 'percentage',
                'value' => 25,
                'max_uses' => 30,
                'used_count' => 0,
                'valid_from' => Carbon::now(),
                'valid_until' => Carbon::now()->addWeeks(2),
                'is_active' => true
            ]
        ];

        foreach ($discounts as $discount) {
            Discount::updateOrCreate(
                ['code' => $discount['code']],
                $discount
            );
        }
    }
}
