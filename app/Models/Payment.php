<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';
    
    protected $fillable = [
        'order_id',
        'payment_date',
        'payment_method',
        'payment_status',
    ];
    
    protected $casts = [
        'payment_date' => 'datetime',
    ];
    
    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}
