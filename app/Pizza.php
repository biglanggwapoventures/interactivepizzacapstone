<?php

namespace App;

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
}
