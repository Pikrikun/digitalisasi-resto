<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantOption extends Model
{
    protected $fillable = ['product_variant_id', 'name', 'additional_price'];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}