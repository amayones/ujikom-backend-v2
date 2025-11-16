<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Seat;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        try {
            $customer = User::where('email', 'customer@test.com')->first();
            $cashier = User::where('email', 'cashier@test.com')->first();
            
            if (!$customer || !$cashier) {
                \Log::warning('OrderSeeder: Customer or Cashier user not found');
                return;
            }
            
            $schedule = Schedule::first();
            if (!$schedule) {
                \Log::warning('OrderSeeder: No schedule found');
                return;
            }
            
            $seats = Seat::where('studio_id', $schedule->studio_id)
                ->limit(2)
                ->get();
            
            if ($seats->isEmpty()) {
                \Log::warning('OrderSeeder: No seats found');
                return;
            }

            // Order 1: Customer Online
            $order1 = Order::create([
                'order_number' => 'ORD-SAMPLE001',
                'user_id' => $customer->id,
                'schedule_id' => $schedule->id,
                'total_amount' => 70000,
                'payment_status' => 'paid',
                'order_type' => 'online',
            ]);

            OrderItem::create([
                'order_id' => $order1->id,
                'seat_id' => $seats[0]->id,
                'price' => 35000,
            ]);
            
            OrderItem::create([
                'order_id' => $order1->id,
                'seat_id' => $seats[1]->id,
                'price' => 35000,
            ]);

            // Order 2: Cashier Offline
            $seats2 = Seat::where('studio_id', $schedule->studio_id)
                ->skip(2)
                ->limit(3)
                ->get();
            
            if ($seats2->count() >= 3) {
                $order2 = Order::create([
                    'order_number' => 'OFF-SAMPLE001',
                    'user_id' => $cashier->id,
                    'schedule_id' => $schedule->id,
                    'total_amount' => 105000,
                    'payment_status' => 'paid',
                    'order_type' => 'offline',
                    'customer_name' => 'John Doe',
                    'customer_phone' => '081234567890',
                ]);

                foreach ($seats2 as $seat) {
                    OrderItem::create([
                        'order_id' => $order2->id,
                        'seat_id' => $seat->id,
                        'price' => 35000,
                    ]);
                }
            }
            
            \Log::info('OrderSeeder: Successfully created sample orders');
        } catch (\Exception $e) {
            \Log::error('OrderSeeder error: ' . $e->getMessage());
        }
    }
}
