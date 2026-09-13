<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_users';

    protected $fillable = [
        'name_users',
        'username',
        'email',
        'phone',
        'password',
        'google_id',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id', 'id_users');
    }
}
