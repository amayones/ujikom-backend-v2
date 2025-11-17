<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScanController extends Controller
{
    use ApiResponseTrait;

    public function getAllOrders()
    {
        try {
            $orders = Order::with(['schedule.film', 'schedule.studio', 'orderItems.seat', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse($orders, 'Orders retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch orders: ' . $e->getMessage(), 500);
        }
    }

    public function getMyOrders(Request $request)
    {
        try {
            $cashierId = $request->user()->id;
            
            $orders = Order::with(['schedule.film', 'schedule.studio', 'orderItems.seat'])
                ->where('user_id', $cashierId)
                ->where('order_type', 'offline')
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse($orders, 'Cashier orders retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch orders: ' . $e->getMessage(), 500);
        }
    }

    public function scanAndUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_number' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Masukkan nomor order', 422);
        }

        try {
            $order = Order::with(['schedule.film', 'schedule.studio', 'orderItems.seat', 'user'])
                ->where('order_number', strtoupper($request->order_number))
                ->first();

            if (!$order) {
                return $this->errorResponse('Tiket tidak ditemukan', 404);
            }

            if ($order->payment_status !== 'paid') {
                return $this->errorResponse('Tiket belum dibayar', 400);
            }

            if ($order->ticket_status === 'scanned') {
                return $this->errorResponse(
                    'Tiket sudah digunakan pada ' . $order->scanned_at->format('d/m/Y H:i'),
                    400
                );
            }

            // Validasi waktu tayang (30 menit sebelum film)
            $showTime = \Carbon\Carbon::parse($order->schedule->show_time);
            $now = \Carbon\Carbon::now();
            
            if ($now->lt($showTime->copy()->subMinutes(30))) {
                return $this->errorResponse(
                    'Tiket hanya bisa di-scan 30 menit sebelum jadwal tayang (' . $showTime->format('d/m/Y H:i') . ')',
                    400
                );
            }

            // Update ticket status
            $order->update([
                'ticket_status' => 'scanned',
                'scanned_at' => now(),
                'scanned_by' => $request->user()->id
            ]);

            return $this->successResponse($order, 'Tiket berhasil di-scan. Pelanggan dapat masuk.');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal scan tiket: ' . $e->getMessage(), 500);
        }
    }
}
