<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'name', 'sort_order', 'is_required', 'type'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function options()
    {
        return $this->hasMany(ProductVariantOption::class);
    }
}