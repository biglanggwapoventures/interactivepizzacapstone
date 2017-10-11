<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomPizzaOrderDetail extends Model
{
    protected $fillable = [
        'custom_pizza_order_id',
        'ingredient_id',
    ];

    public function customOrder()
    {
        return $this->belongsTo('App\CustomPizzaOrder', 'custom_pizza_order_id');
    }

    public function ingredients()
    {
        return $this->belongsTo('App\Ingredient', 'ingredient_id');
    }
}
