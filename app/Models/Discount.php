<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'max_uses',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active'
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
        'value' => 'decimal:2'
    ];

    public function isValid()
    {
        $now = now()->startOfDay();
        
        if (!$this->is_active) {
            return false;
        }
        
        if ($now->lt($this->valid_from) || $now->gt($this->valid_until)) {
            return false;
        }
        
        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }
        
        return true;
    }

    public function calculateDiscount($amount)
    {
        if ($this->type === 'percentage') {
            return $amount * ($this->value / 100);
        }
        
        return min($this->value, $amount);
    }
}
