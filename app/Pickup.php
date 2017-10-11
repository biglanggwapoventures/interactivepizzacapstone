<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pickup extends Model
{
    protected $fillable = [
        'order_id',
        'recipient',
        'estimated_pickup_time',
    ];

    public function order()
    {
        return $this->belongsTo('App\Order', 'order_id');
    }
}
