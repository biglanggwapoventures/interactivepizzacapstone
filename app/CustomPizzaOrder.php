<?php

namespace App;

use DB;
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

    public function decrementStocks()
    {
        DB::transaction(function () {
            $this->usedIngredients->each->decrementStock();
        }, 3);
    }
}
