<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'street_number',
        'barangay',
        'city',
        'contact_number',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
