<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['id'];

    public function products()
    {
        return $this->belongsToMany(Product::class, table:'order_products')
        ->withPivot('quantity')
        ->withTimestamps();
    }
}
