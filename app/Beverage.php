<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Beverage extends Model
{
    protected $table = 'ingredients';

    protected $fillable = [
        'description',
        'unit_price',
        'is_beverage',
    ];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('beverage', function (Builder $builder) {
            $builder->whereIsBeverage(1);
        });
    }

}
