<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Film extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'genre',
        'duration',
        'status',
        'description',
        'poster',
        'trailer',
        'base_price',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}