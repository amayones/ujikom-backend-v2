<?php

namespace App\Helpers;

use App\Models\Order;

class OrderHelper
{
    public static function generateOrderNumber($prefix = 'ORD')
    {
        do {
            $orderNumber = $prefix . '-' . date('YmdHis') . '-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        } while (Order::where('order_number', $orderNumber)->exists());
        
        return $orderNumber;
    }
}
