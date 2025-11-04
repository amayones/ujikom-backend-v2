<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    private function initMidtrans()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function getSnapToken($orderId)
    {
        $this->initMidtrans();
        
        try {
            $order = Order::with(['orderItems.seat', 'schedule.film', 'user'])->findOrFail($orderId);

            $itemDetails = [];
            foreach ($order->orderItems as $item) {
                $itemDetails[] = [
                    'id' => 'SEAT-' . $item->seat_id,
                    'price' => (int) $item->price,
                    'quantity' => 1,
                    'name' => "Seat {$item->seat->row}{$item->seat->column}",
                ];
            }

            // All payment methods for both customer and cashier
            $enabledPayments = ['credit_card', 'bca_va', 'bni_va', 'bri_va', 'permata_va', 'other_va', 'gopay', 'shopeepay', 'qris', 'cimb_clicks', 'bca_klikbca', 'bca_klikpay', 'mandiri_clickpay', 'echannel', 'indomaret', 'alfamart'];

            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) $order->total_amount,
                ],
                'customer_details' => [
                    'first_name' => $order->user->name,
                    'email' => $order->user->email,
                    'phone' => $order->user->phone ?? '081234567890',
                ],
                'item_details' => $itemDetails,
                'enabled_payments' => $enabledPayments,
            ];
            
            $snapToken = Snap::getSnapToken($params);
            
            return $this->successResponse(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            \Log::error('Snap Token Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function callback(Request $request)
    {
        try {
            \Log::info('Midtrans Callback', $request->all());
            
            $serverKey = config('midtrans.server_key');
            $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
            
            if ($hashed !== $request->signature_key) {
                \Log::error('Invalid Signature', [
                    'expected' => $hashed,
                    'received' => $request->signature_key
                ]);
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            $order = Order::where('order_number', $request->order_id)->first();
            if (!$order) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            $transactionStatus = $request->transaction_status;
            $fraudStatus = $request->fraud_status ?? 'accept';

            if ($transactionStatus == 'capture') {
                $order->payment_status = ($fraudStatus == 'accept') ? 'paid' : 'failed';
            } elseif ($transactionStatus == 'settlement') {
                $order->payment_status = 'paid';
            } elseif ($transactionStatus == 'pending') {
                $order->payment_status = 'pending';
            } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                $order->payment_status = 'failed';
            }

            $order->save();
            
            \Log::info('Order Updated', [
                'order_number' => $order->order_number,
                'status' => $order->payment_status
            ]);
            
            return response()->json(['message' => 'OK']);
        } catch (\Exception $e) {
            \Log::error('Callback Error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function checkStatus($orderNumber)
    {
        $this->initMidtrans();
        
        try {
            $order = Order::where('order_number', $orderNumber)->first();
            
            if (!$order) {
                return $this->errorResponse('Order not found', 404);
            }

            try {
                $status = Transaction::status($orderNumber);
                $transactionStatus = $status->transaction_status;
                $fraudStatus = $status->fraud_status ?? 'accept';

                if ($transactionStatus == 'capture') {
                    $order->payment_status = ($fraudStatus == 'accept') ? 'paid' : 'failed';
                } elseif ($transactionStatus == 'settlement') {
                    $order->payment_status = 'paid';
                } elseif ($transactionStatus == 'pending') {
                    $order->payment_status = 'pending';
                } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
                    $order->payment_status = 'failed';
                }

                $order->save();
            } catch (\Exception $e) {
                \Log::warning('Cannot check Midtrans status', ['error' => $e->getMessage()]);
            }
            
            return $this->successResponse(['order' => $order->load(['orderItems.seat', 'schedule.film'])]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getClientKey()
    {
        return $this->successResponse(['client_key' => config('midtrans.client_key')]);
    }
}
