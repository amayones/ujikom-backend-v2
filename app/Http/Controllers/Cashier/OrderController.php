<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Price;
use App\Models\Seat;
use App\Models\Schedule;
use App\Helpers\OrderHelper;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class OrderController extends Controller
{
    use ApiResponseTrait;

    public function offlineOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required|exists:schedules,id',
            'seat_ids' => 'required|array',
            'seat_ids.*' => 'exists:seats,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:15'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        try {
            DB::beginTransaction();

            $schedule = Schedule::with(['film', 'studio'])->find($request->schedule_id);
            $seats = Seat::whereIn('id', $request->seat_ids)->lockForUpdate()->get();
            
            // Check if seats are available (including pending orders)
            $bookedSeats = OrderItem::whereHas('order', function($query) use ($request) {
                $query->where('schedule_id', $request->schedule_id)
                      ->where(function($q) {
                          $q->where('payment_status', 'paid')
                            ->orWhere(function($q2) {
                                $q2->where('payment_status', 'pending')
                                   ->where('created_at', '>', now()->subMinutes(10));
                            });
                      });
            })->whereIn('seat_id', $request->seat_ids)->exists();

            if ($bookedSeats) {
                DB::rollback();
                return $this->errorResponse('Some seats are already booked or being processed', 400);
            }

            // Calculate total price
            $showDate = Carbon::parse($schedule->show_time);
            $dayType = $showDate->isWeekend() ? 'weekend' : 'weekday';
            
            // Fetch all prices at once to avoid N+1 query
            $prices = Price::where('day_type', $dayType)
                          ->whereIn('seat_category', $seats->pluck('category')->unique())
                          ->get()
                          ->keyBy('seat_category');
            
            $totalAmount = 0;
            $orderItems = [];

            foreach ($seats as $seat) {
                $price = $prices->get($seat->category);
                $seatPrice = $price ? $price->price : $schedule->film->base_price;
                $totalAmount += $seatPrice;
                
                $orderItems[] = [
                    'seat_id' => $seat->id,
                    'price' => $seatPrice
                ];
            }

            // Create order with customer info
            $order = Order::create([
                'order_number' => OrderHelper::generateOrderNumber('OFF'),
                'user_id' => $request->user()->id,
                'schedule_id' => $request->schedule_id,
                'total_amount' => $totalAmount,
                'payment_status' => 'paid',
                'order_type' => 'offline',
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone
            ]);

            // Create order items
            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'seat_id' => $item['seat_id'],
                    'price' => $item['price']
                ]);
            }

            DB::commit();

            $order->load(['orderItems.seat', 'schedule.film', 'schedule.studio']);

            return $this->successResponse($order, 'Offline order created successfully', 201);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Cashier offline order error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Failed to create offline order: ' . $e->getMessage(), 500);
        }
    }

    public function processOnline($orderId)
    {
        $order = Order::with(['orderItems.seat', 'schedule.film', 'schedule.studio'])
                     ->find($orderId);

        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }

        if ($order->payment_status === 'paid') {
            return $this->errorResponse('Order already processed', 400);
        }

        $order->update(['payment_status' => 'paid']);

        return $this->successResponse($order, 'Online order processed successfully');
    }

    public function printTicket($orderId)
    {
        $order = Order::with(['orderItems.seat', 'schedule.film', 'schedule.studio', 'user'])
                     ->find($orderId);

        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }

        if ($order->payment_status !== 'paid') {
            return $this->errorResponse('Order not paid yet', 400);
        }

        return $this->successResponse($order, 'Ticket ready for print');
    }

    public function onlineOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'schedule_id' => 'required|exists:schedules,id',
            'seat_ids' => 'required|array',
            'seat_ids.*' => 'exists:seats,id'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        try {
            DB::beginTransaction();

            $schedule = Schedule::with(['film', 'studio'])->find($request->schedule_id);
            $seats = Seat::whereIn('id', $request->seat_ids)->lockForUpdate()->get();
            
            $bookedSeats = OrderItem::whereHas('order', function($query) use ($request) {
                $query->where('schedule_id', $request->schedule_id)
                      ->where('payment_status', 'paid');
            })->whereIn('seat_id', $request->seat_ids)->exists();

            if ($bookedSeats) {
                return $this->errorResponse('Some seats are already booked', 400);
            }

            $showDate = Carbon::parse($schedule->show_time);
            $dayType = $showDate->isWeekend() ? 'weekend' : 'weekday';
            
            $prices = Price::where('day_type', $dayType)
                          ->whereIn('seat_category', $seats->pluck('category')->unique())
                          ->get()
                          ->keyBy('seat_category');
            
            $totalAmount = 0;
            $orderItems = [];

            foreach ($seats as $seat) {
                $price = $prices->get($seat->category);
                $seatPrice = $price ? $price->price : $schedule->film->base_price;
                $totalAmount += $seatPrice;
                
                $orderItems[] = [
                    'seat_id' => $seat->id,
                    'price' => $seatPrice
                ];
            }

            $order = Order::create([
                'order_number' => OrderHelper::generateOrderNumber('CSH'),
                'user_id' => $request->user()->id,
                'schedule_id' => $request->schedule_id,
                'total_amount' => $totalAmount,
                'payment_status' => 'pending',
                'order_type' => 'cashier_online'
            ]);

            foreach ($orderItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'seat_id' => $item['seat_id'],
                    'price' => $item['price']
                ]);
            }

            DB::commit();

            $order->load(['orderItems.seat', 'schedule.film', 'schedule.studio', 'user']);

            return $this->successResponse(['order' => $order], 'Order created successfully', 201);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Cashier online order error: ' . $e->getMessage());
            return $this->errorResponse('Failed to create order: ' . $e->getMessage(), 500);
        }
    }

    public function scanTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_number' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors());
        }

        try {
            $order = Order::with(['orderItems.seat', 'schedule.film', 'schedule.studio', 'user'])
                         ->where('order_number', $request->order_number)
                         ->first();

            if (!$order) {
                return $this->errorResponse('Tiket tidak ditemukan', 404);
            }

            if ($order->payment_status !== 'paid') {
                return $this->errorResponse('Tiket belum dibayar', 400);
            }

            if ($order->ticket_status === 'scanned') {
                return $this->errorResponse(
                    'Tiket sudah digunakan pada ' . Carbon::parse($order->scanned_at)->format('d/m/Y H:i'),
                    400
                );
            }

            // Check if show time has passed
            $showTime = Carbon::parse($order->schedule->show_time);
            if (Carbon::now()->lt($showTime->subMinutes(30))) {
                return $this->errorResponse('Tiket hanya bisa di-scan 30 menit sebelum jadwal tayang', 400);
            }

            // Update ticket status
            $order->update([
                'ticket_status' => 'scanned',
                'scanned_at' => now(),
                'scanned_by' => $request->user()->id
            ]);

            $order->load('scannedBy');

            return $this->successResponse($order, 'Tiket berhasil di-scan. Pelanggan dapat masuk.');

        } catch (\Exception $e) {
            \Log::error('Scan ticket error: ' . $e->getMessage());
            return $this->errorResponse('Gagal scan tiket: ' . $e->getMessage(), 500);
        }
    }
}