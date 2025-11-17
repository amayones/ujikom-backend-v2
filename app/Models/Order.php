<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'ticket_status',
        'scanned_at',
        'scanned_by',
    ];

    protected $appends = ['is_expired', 'expires_at'];

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

    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    public function getIsExpiredAttribute()
    {
        if ($this->payment_status !== 'pending') {
            return false;
        }
        return Carbon::parse($this->created_at)->addMinutes(5)->isPast();
    }

    public function getExpiresAtAttribute()
    {
        if ($this->payment_status !== 'pending') {
            return null;
        }
        return Carbon::parse($this->created_at)->addMinutes(5)->toIso8601String();
    }

    public function scopeExpired($query)
    {
        return $query->where('payment_status', 'pending')
                    ->where('created_at', '<', Carbon::now()->subMinutes(5));
    }
}