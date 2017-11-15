<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveryPersonnel extends Model
{
    protected $fillable = [
        'firstname',
        'lastname',
        'mobile_number',
        'remarks',
    ];

    protected $append = [
        'fullname',
    ];

    public function getFullnameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    public static function toList()
    {
        return self::select('firstname', 'lastname', 'id')
            ->get()
            ->pluck('fullname', 'id');
    }
}
