<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = [
        'order_id',
        'delivery_personnel_id',
        'estimated_delivery_time',
        'street',
        'barangay',
        'city',
        'landmark',
        'destination_type',
        'cash_amount',
    ];

    public function order()
    {
        return $this->belongsTo('App\Order', 'order_id');
    }
}
