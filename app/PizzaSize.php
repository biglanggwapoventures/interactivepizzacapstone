<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PizzaSize extends Model
{
    protected $fillable = [
        'pizza_id',
        'size',
        'unit_price',
    ];

    public function ingredients()
    {
        return $this->belongsToMany('App\Ingredient', 'pizza_ingredients', 'pizza_size_id', 'ingredient_id')
        // ->withDefault([])
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function pizza()
    {
        return $this->belongsTo('App\Pizza', 'pizza_id');
    }
}
