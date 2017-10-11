<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PremadePizzaOrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'pizza_size_id',
        'quantity',
    ];
    public function order()
    {
        return $this->belongsTo('App\Order', 'order_id');
    }

    public function pizzaSize()
    {
        return $this->belongsTo('App\PizzaSize', 'pizza_size_id');
    }
}
