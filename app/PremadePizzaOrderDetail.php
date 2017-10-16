<?php

namespace App;

use DB;
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

    public function decrementStocks()
    {
        return DB::table('ingredients AS i')
            ->join('pizza_ingredients AS pi', 'pi.ingredient_id', '=', 'i.id')
            ->where('pi.id', $this->pizza_size_id)
            ->update([
                'i.remaining_quantity' => DB::raw("i.remaining_quantity - {$this->quantity} * pi.quantity"),
            ]);
    }
}
