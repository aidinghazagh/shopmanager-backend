<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ['created_at', 'updated_at'];
    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products')
            ->using(OrderProduct::class)
            ->withPivot(['name_on_created', 'price_on_created', 'purchase_price_on_created', 'quantity']);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    // Helper method to check if the order is locked (has any payments)
    public function isLocked()
    {
        return $this->payments()->count() > 0;
    }
}
