<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'schedule_id',
        'total_amount',
        'payment_status',
        'order_type',
        'customer_name',
        'customer_phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}