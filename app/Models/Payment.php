<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $guarded = ['updated_at', 'created_at'];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
