<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'restaurant_id', 'table_id', 'customer_name',
        'total', 'status', 'payment_method', 'payment_proof', 'note', 'expires_at', 'cancel_reason',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}