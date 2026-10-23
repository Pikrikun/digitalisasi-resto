<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',           // ← TAMBAHAN
        'restaurant_id',  // ← TAMBAHAN
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    /**
     * User yang jadi owner punya 1 Restaurant sendiri
     * (Restaurant yang owner_id-nya = id user ini)
     */
    public function ownedRestaurant()
    {
        return $this->hasOne(Restaurant::class, 'owner_id');
    }
}