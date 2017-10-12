<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'lastname', 'email', 'password', 'login_type', 'banned_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = [
        'fullname',
    ];

    public function getFullnameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    public function isAdmin()
    {
        return $this->login_type === 'ADMIN';
    }

    public function profile()
    {
        return $this->hasOne('App\Profile', 'user_id');
    }
}
