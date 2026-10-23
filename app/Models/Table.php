<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = ['restaurant_id', 'name', 'capacity', 'token'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

