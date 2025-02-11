<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderProduct extends Pivot
{
    protected $table = 'order_products';
    public $timestamps = true;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
