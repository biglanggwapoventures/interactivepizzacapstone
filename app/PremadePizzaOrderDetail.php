<?php

namespace App;

use DB;
// use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
        DB::enableQueryLog();
        Log::info("Decrementing {$this->quantity} for order # {$this->order_id}");
        Log::info(DB::getQueryLog());
        return DB::table('pizza_ingredients AS pi')
            ->join('ingredients AS i', 'i.id', '=', 'pi.ingredient_id')
            ->where('pi.pizza_size_id', $this->pizza_size_id)
            ->update([
                'i.remaining_quantity' => DB::raw("(i.remaining_quantity - ({$this->quantity} * pi.quantity))"),
            ]);
    }
}
