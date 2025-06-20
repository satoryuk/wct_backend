<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    protected $primaryKey = 'inventory_id';
    
    protected $fillable = [
        'product_id',
        'stock_in',
        'stock_out',
    ];
    
    protected $casts = [
        'order_date' => 'datetime',
    ];
    
    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
