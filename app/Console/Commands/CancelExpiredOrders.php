<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CancelExpiredOrders extends Command
{
    protected $signature = 'orders:cancel-expired';
    protected $description = 'Cancel orders that are pending for more than 5 minutes';

    public function handle()
    {
        $expiredOrders = Order::where('payment_status', 'pending')
            ->where('created_at', '<', Carbon::now()->subMinutes(5))
            ->get();

        $count = $expiredOrders->count();

        foreach ($expiredOrders as $order) {
            $order->update(['payment_status' => 'cancelled']);
        }

        $this->info("Cancelled {$count} expired orders");
        
        return 0;
    }
}
