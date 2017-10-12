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
        'custom_unit_price_small',
        'custom_unit_price_medium',
        'custom_unit_price_large',
        'custom_quantity_needed_small',
        'custom_quantity_needed_medium',
        'custom_quantity_needed_large',
    ];

    protected $appends = [
        'customized_prices',
        'customized_quantities',
    ];

    public function getCustomizedPricesAttribute()
    {
        return [
            'SMALL' => $this->custom_unit_price_small,
            'MEDIUM' => $this->custom_unit_price_medium,
            'LARGE' => $this->custom_unit_price_large,
        ];
    }

    public function getCustomizedQuantitiesAttribute()
    {
        return [
            'SMALL' => $this->custom_quantity_needed_small,
            'MEDIUM' => $this->custom_quantity_needed_medium,
            'LARGE' => $this->custom_quantity_needed_large,
        ];
    }

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
