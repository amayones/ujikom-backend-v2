<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Price;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('email', 'customer@test.com')->first();
        $cashier = User::where('email', 'cashier@test.com')->first();
        $schedules = Schedule::with('film')->get();
        
        if ($schedules->isEmpty()) {
            return;
        }

        // Order 1: Customer Online - Paid
        $schedule1 = $schedules->first();
        $seats1 = Seat::where('studio_id', $schedule1->studio_id)
            ->whereIn('row', ['A'])
            ->whereIn('column', [1, 2])
            ->get();
        
        $showDate1 = Carbon::parse($schedule1->show_time);
        $dayType1 = $showDate1->isWeekend() ? 'weekend' : 'weekday';
        $price1 = Price::where('day_type', $dayType1)->first();
        $seatPrice1 = $price1 ? $price1->price : 35000;
        
        $order1 = Order::create([
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'user_id' => $customer->id,
            'schedule_id' => $schedule1->id,
            'total_amount' => $seatPrice1 * $seats1->count(),
            'payment_status' => 'paid',
            'order_type' => 'online',
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        foreach ($seats1 as $seat) {
            OrderItem::create([
                'order_id' => $order1->id,
                'seat_id' => $seat->id,
                'price' => $seatPrice1,
            ]);
        }

        // Order 2: Cashier Offline - Paid
        if ($schedules->count() > 1) {
            $schedule2 = $schedules->skip(1)->first();
            $seats2 = Seat::where('studio_id', $schedule2->studio_id)
                ->whereIn('row', ['B'])
                ->whereIn('column', [3, 4, 5])
                ->get();
            
            $showDate2 = Carbon::parse($schedule2->show_time);
            $dayType2 = $showDate2->isWeekend() ? 'weekend' : 'weekday';
            $price2 = Price::where('day_type', $dayType2)->first();
            $seatPrice2 = $price2 ? $price2->price : 35000;
            
            $order2 = Order::create([
                'order_number' => 'OFF-' . strtoupper(uniqid()),
                'user_id' => $cashier->id,
                'schedule_id' => $schedule2->id,
                'total_amount' => $seatPrice2 * $seats2->count(),
                'payment_status' => 'paid',
                'order_type' => 'offline',
                'customer_name' => 'John Doe',
                'customer_phone' => '081234567890',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ]);

            foreach ($seats2 as $seat) {
                OrderItem::create([
                    'order_id' => $order2->id,
                    'seat_id' => $seat->id,
                    'price' => $seatPrice2,
                ]);
            }
        }

        // Order 3: Customer Online - Paid (Recent)
        if ($schedules->count() > 2) {
            $schedule3 = $schedules->skip(2)->first();
            $seats3 = Seat::where('studio_id', $schedule3->studio_id)
                ->whereIn('row', ['C'])
                ->whereIn('column', [1, 2, 3, 4])
                ->get();
            
            $showDate3 = Carbon::parse($schedule3->show_time);
            $dayType3 = $showDate3->isWeekend() ? 'weekend' : 'weekday';
            $price3 = Price::where('day_type', $dayType3)->first();
            $seatPrice3 = $price3 ? $price3->price : 35000;
            
            $order3 = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'user_id' => $customer->id,
                'schedule_id' => $schedule3->id,
                'total_amount' => $seatPrice3 * $seats3->count(),
                'payment_status' => 'paid',
                'order_type' => 'online',
                'created_at' => now()->subHours(5),
                'updated_at' => now()->subHours(5),
            ]);

            foreach ($seats3 as $seat) {
                OrderItem::create([
                    'order_id' => $order3->id,
                    'seat_id' => $seat->id,
                    'price' => $seatPrice3,
                ]);
            }
        }

        // Order 4: Cashier Offline - Paid (Recent)
        if ($schedules->count() > 3) {
            $schedule4 = $schedules->skip(3)->first();
            $seats4 = Seat::where('studio_id', $schedule4->studio_id)
                ->whereIn('row', ['D'])
                ->whereIn('column', [5, 6])
                ->get();
            
            $showDate4 = Carbon::parse($schedule4->show_time);
            $dayType4 = $showDate4->isWeekend() ? 'weekend' : 'weekday';
            $price4 = Price::where('day_type', $dayType4)->first();
            $seatPrice4 = $price4 ? $price4->price : 35000;
            
            $order4 = Order::create([
                'order_number' => 'OFF-' . strtoupper(uniqid()),
                'user_id' => $cashier->id,
                'schedule_id' => $schedule4->id,
                'total_amount' => $seatPrice4 * $seats4->count(),
                'payment_status' => 'paid',
                'order_type' => 'offline',
                'customer_name' => 'Jane Smith',
                'customer_phone' => '082345678901',
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ]);

            foreach ($seats4 as $seat) {
                OrderItem::create([
                    'order_id' => $order4->id,
                    'seat_id' => $seat->id,
                    'price' => $seatPrice4,
                ]);
            }
        }
    }
}
