<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $startDate = $request->get('start_date') 
            ? Carbon::parse($request->get('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        \Log::info('Report Date Range', [
            'start' => $startDate->toDateTimeString(),
            'end' => $endDate->toDateTimeString()
        ]);

        // Get all orders with details
        $orders = Order::with(['user', 'schedule.film', 'orderItems.seat'])
                      ->where('payment_status', 'paid')
                      ->where('created_at', '>=', $startDate)
                      ->where('created_at', '<=', $endDate)
                      ->orderBy('created_at', 'desc')
                      ->get();

        \Log::info('Orders Found', ['count' => $orders->count()]);

        // Income from orders
        $income = $orders->sum('total_amount');

        // Transactions detail
        $transactions = $orders->map(function($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->user->name,
                'film_title' => $order->schedule->film->title ?? 'N/A',
                'total_amount' => $order->total_amount,
                'seats_count' => $order->orderItems->count(),
                'seats' => $order->orderItems->map(fn($item) => $item->seat->row . $item->seat->column)->join(', '),
                'payment_status' => $order->payment_status,
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $data = [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'summary' => [
                'total_income' => $income,
                'total_transactions' => $orders->count()
            ],
            'transactions' => $transactions
        ];

        return $this->successResponse($data, 'Report retrieved successfully');
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date') 
            ? Carbon::parse($request->get('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        // Get all orders with details
        $orders = Order::with(['user', 'schedule.film', 'orderItems.seat'])
                      ->where('payment_status', 'paid')
                      ->where('created_at', '>=', $startDate)
                      ->where('created_at', '<=', $endDate)
                      ->orderBy('created_at', 'desc')
                      ->get();

        $income = $orders->sum('total_amount');

        $transactions = $orders->map(function($order) {
            return [
                'order_number' => $order->order_number,
                'customer_name' => $order->user->name,
                'film_title' => $order->schedule->film->title ?? 'N/A',
                'total_amount' => $order->total_amount,
                'seats' => $order->orderItems->map(fn($item) => $item->seat->row . $item->seat->column)->join(', '),
                'created_at' => $order->created_at->format('d/m/Y'),
            ];
        });

        $data = [
            'period' => [
                'start_date' => $startDate->format('d/m/Y'),
                'end_date' => $endDate->format('d/m/Y')
            ],
            'summary' => [
                'total_income' => $income,
                'total_transactions' => $orders->count()
            ],
            'transactions' => $transactions,
            'generated_at' => Carbon::now()->format('d/m/Y H:i:s')
        ];

        $pdf = Pdf::loadView('pdf.reports', $data)
                  ->setPaper('a4', 'landscape')
                  ->setOption('margin-top', 10)
                  ->setOption('margin-bottom', 10);
        
        return $pdf->download('Laporan-Keuangan-' . $startDate->format('d-m-Y') . '-sd-' . $endDate->format('d-m-Y') . '.pdf');
    }
}