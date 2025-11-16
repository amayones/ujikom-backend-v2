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
        $period = $request->get('period', 'month');
        
        if ($period === 'day') {
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } elseif ($period === 'year') {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
        } else {
            $startDate = $request->get('start_date') 
                ? Carbon::parse($request->get('start_date'))->startOfDay()
                : Carbon::now()->startOfMonth();
            $endDate = $request->get('end_date')
                ? Carbon::parse($request->get('end_date'))->endOfDay()
                : Carbon::now()->endOfDay();
        }

        $orders = Order::with(['user', 'schedule.film', 'orderItems.seat'])
                      ->where('payment_status', 'paid')
                      ->where('created_at', '>=', $startDate)
                      ->where('created_at', '<=', $endDate)
                      ->orderBy('created_at', 'desc')
                      ->get();

        $totalRevenue = $orders->sum('total_amount');
        $totalTransactions = $orders->count();
        $totalTickets = $orders->sum(fn($o) => $o->orderItems->count());
        $avgTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Revenue chart data
        $revenueChart = [];
        if ($period === 'day') {
            for ($i = 0; $i < 24; $i++) {
                $hour = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                $revenue = $orders->filter(fn($o) => $o->created_at->hour == $i)->sum('total_amount');
                $revenueChart[] = ['label' => $hour, 'revenue' => $revenue];
            }
        } elseif ($period === 'month') {
            $days = $startDate->daysInMonth;
            for ($i = 1; $i <= $days; $i++) {
                $revenue = $orders->filter(fn($o) => $o->created_at->day == $i)->sum('total_amount');
                $revenueChart[] = ['label' => $i, 'revenue' => $revenue];
            }
        } else {
            for ($i = 1; $i <= 12; $i++) {
                $monthName = Carbon::create()->month($i)->format('M');
                $revenue = $orders->filter(fn($o) => $o->created_at->month == $i)->sum('total_amount');
                $revenueChart[] = ['label' => $monthName, 'revenue' => $revenue];
            }
        }

        // Transaction chart data
        $transactionChart = [];
        if ($period === 'day') {
            for ($i = 0; $i < 24; $i++) {
                $hour = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                $count = $orders->filter(fn($o) => $o->created_at->hour == $i)->count();
                $transactionChart[] = ['label' => $hour, 'count' => $count];
            }
        } elseif ($period === 'month') {
            $days = $startDate->daysInMonth;
            for ($i = 1; $i <= $days; $i++) {
                $count = $orders->filter(fn($o) => $o->created_at->day == $i)->count();
                $transactionChart[] = ['label' => $i, 'count' => $count];
            }
        } else {
            for ($i = 1; $i <= 12; $i++) {
                $monthName = Carbon::create()->month($i)->format('M');
                $count = $orders->filter(fn($o) => $o->created_at->month == $i)->count();
                $transactionChart[] = ['label' => $monthName, 'count' => $count];
            }
        }

        // Top films
        $topFilms = $orders->groupBy('schedule.film.title')
            ->map(fn($group) => [
                'name' => $group->first()->schedule->film->title ?? 'N/A',
                'revenue' => $group->sum('total_amount')
            ])
            ->sortByDesc('revenue')
            ->take(5)
            ->values();

        // Order types
        $orderTypes = [
            ['name' => 'Customer Online', 'count' => $orders->where('order_type', 'online')->count()],
            ['name' => 'Kasir Tunai', 'count' => $orders->where('order_type', 'offline')->count()],
        ];

        // Transactions detail
        $transactions = $orders->map(function($order) {
            return [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->user->name ?? $order->customer_name ?? 'N/A',
                'film_title' => $order->schedule->film->title ?? 'N/A',
                'total_amount' => $order->total_amount,
                'seats_count' => $order->orderItems->count(),
                'seats' => $order->orderItems->map(fn($item) => $item->seat->row . $item->seat->column)->join(', '),
                'payment_status' => $order->payment_status,
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            ];
        });

        $data = [
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'total_tickets' => $totalTickets,
            'avg_transaction' => $avgTransaction,
            'revenue_chart' => $revenueChart,
            'transaction_chart' => $transactionChart,
            'top_films' => $topFilms,
            'order_types' => $orderTypes,
            'transactions' => $transactions,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'summary' => [
                'total_income' => $totalRevenue,
                'total_transactions' => $totalTransactions
            ]
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