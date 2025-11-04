<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'studio_id',
        'row',
        'column',
        'category',
    ];

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}