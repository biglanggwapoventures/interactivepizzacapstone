<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = [
        'description',
        'ingredient_category_id',
        'unit_price',
        'photo',
    ];

    public function category()
    {
        return $this->belongsTo('App\IngredientCategory', 'ingredient_category_id');
    }

    public function scopeAlphabetized($query)
    {
        return $query->orderBy('description');
    }

    public function getPhotoAttribute($value)
    {
        return asset("storage/{$value}");
    }
}
