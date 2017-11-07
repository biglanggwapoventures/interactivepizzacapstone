<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $fillable = [
        'user_id',
        'message',
        'is_read',
    ];

    public function owner()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
