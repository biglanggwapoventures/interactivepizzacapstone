<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomPizzaOrder extends Model
{
    protected $fillable = [
        'order_id',
        'size',
        'quantity',
    ];

    public function order()
    {
        return $this->belongsTo('App\Order', 'order_id');
    }

    public function usedIngredients()
    {
        return $this->hasMany('App\CustomPizzaOrderDetail', 'custom_pizza_order_id');
    }
}
