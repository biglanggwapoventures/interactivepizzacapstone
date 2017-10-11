<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IngredientCategory extends Model
{
    protected $fillable = [
        'description',
        'custom_pizza_sequence',
    ];

    public function ingredients()
    {
        return $this->hasMany('App\Ingredient', 'ingredient_category_id')->orderBy('description');
    }

    public static function toList()
    {
        return self::select('description', 'id')
            ->alphabetized()
            ->pluck('description', 'id');
    }

    public function scopeAlphabetized($query)
    {
        return $query->orderBy('description');
    }

    public function scopeCustomOrderSequenced($query)
    {
        return $query->orderBy('custom_pizza_sequence');
    }
}
