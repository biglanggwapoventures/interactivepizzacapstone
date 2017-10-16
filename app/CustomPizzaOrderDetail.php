<?php

namespace App;

use DB;
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

    public function decrementStock()
    {
        return DB::table('ingredients AS i')
            ->join('custom_pizza_order_details AS cpod', 'cpod.ingredient_id', '=', 'i.id')
            ->join('custom_pizza_orders AS cpo', 'cpo.id', '=', 'cpod.custom_pizza_order_id')
            ->where('cpod.id', $this->id)
            ->update([
                'i.remaining_quantity' => DB::raw('(CASE WHEN cpo.size = "SMALL" THEN i.remaining_quantity - (i.custom_quantity_needed_small * cpo.quantity) WHEN cpo.size = "MEDIUM" THEN i.remaining_quantity - (i.custom_quantity_needed_medium * cpo.quantity) ELSE i.remaining_quantity - (i.custom_quantity_needed_large * cpo.quantity) END)'),
            ]);
    }
}
