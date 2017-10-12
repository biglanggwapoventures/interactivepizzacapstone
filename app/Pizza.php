<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class Pizza extends Model
{
    protected $fillable = [
        'name',
        'description',
        'photo',
    ];

    protected $appends = [
        // 'ingredients',
    ];

    public function sizes()
    {
        return $this->hasMany('App\PizzaSize', 'pizza_id');
    }

    public function getPhotoAttribute($value)
    {
        return asset("storage/{$value}");
    }

    public function getIngredients()
    {
        $clone = clone $this;
        $this->ingredients = $clone->sizes->load('ingredients')->pluck('ingredients')->flatten()->pluck('description', 'id');
        unset($clone);
    }

    public static function getSellableQuantities()
    {
        $result = DB::table('pizza_ingredients AS pi')
            ->select('pi.pizza_size_id')
            ->addSelect(DB::raw('TRUNCATE(MIN(i.remaining_quantity / pi.quantity), 0) AS total_sellable'))
            ->join('ingredients AS i', 'i.id', '=', 'pi.ingredient_id')
            ->groupBy('pi.pizza_size_id')
            ->get();

        return $result->pluck('total_sellable', 'pizza_size_id');
    }

}
