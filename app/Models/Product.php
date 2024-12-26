<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $guarded = ['created_at', 'updated_at'];
    public function inventoryLogs()
    {
        return $this->hasMany(ProductInventoryLog::class);
    }
    public function logs()
    {
        return $this->hasMany(ProductLog::class);
    }
    public function getInventoryAttribute()
    {
        return (int) $this->inventoryLogs()->sum('quantity_change');
    }

}
