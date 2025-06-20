<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Brand extends Model

{
    protected $table = 'brand';
    protected $primaryKey = 'brand_id';
    
    protected $fillable = [
        'brand_name',
        'logo',
    ];
    
}
