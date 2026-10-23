<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'restaurant_id', 'category_id', 'name', 'description',
        'price', 'image', 'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}