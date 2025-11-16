<?php

namespace App\Http\Controllers\Customer;

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

    public function checkout(Request $request)
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
            
            // Check if seats are available (excluding cancelled orders)
            $bookedSeats = OrderItem::whereHas('order', function($query) use ($request) {
                $query->where('schedule_id', $request->schedule_id)
                      ->whereIn('payment_status', ['paid', 'pending'])
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
            
            $price = Price::where('day_type', $dayType)->first();
            $seatPrice = $price ? $price->price : $schedule->film->base_price;
            
            $totalAmount = 0;
            $orderItems = [];

            foreach ($seats as $seat) {
                $totalAmount += $seatPrice;
                
                $orderItems[] = [
                    'seat_id' => $seat->id,
                    'price' => $seatPrice
                ];
            }

            // Create order with unique order number
            $order = Order::create([
                'order_number' => OrderHelper::generateOrderNumber('ORD'),
                'user_id' => $request->user()->id,
                'schedule_id' => $request->schedule_id,
                'total_amount' => $totalAmount,
                'payment_status' => 'pending',
                'order_type' => 'online'
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

            $order->load(['orderItems.seat', 'schedule.film', 'schedule.studio', 'user']);

            return $this->successResponse([
                'order' => $order
            ], 'Order created successfully', 201);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Customer checkout error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse('Failed to create order: ' . $e->getMessage(), 500);
        }
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $orders = Order::with(['schedule.film', 'schedule.studio', 'orderItems.seat'])
                      ->where('user_id', $request->user()->id)
                      ->orderBy('created_at', 'desc')
                      ->paginate($perPage);

        return $this->successResponse($orders, 'Orders retrieved successfully');
    }

    public function show(Request $request, $id)
    {
        $order = Order::with(['schedule.film', 'schedule.studio', 'orderItems.seat'])
                     ->where('user_id', $request->user()->id)
                     ->find($id);

        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }

        return $this->successResponse($order, 'Order detail retrieved successfully');
    }

    public function cancel(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)->find($id);

        if (!$order) {
            return $this->errorResponse('Order not found', 404);
        }

        if ($order->payment_status === 'paid') {
            return $this->errorResponse('Cannot cancel paid order', 400);
        }

        $order->update(['payment_status' => 'cancelled']);

        return $this->successResponse($order, 'Order cancelled successfully');
    }
}