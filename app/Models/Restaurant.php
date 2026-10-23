<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi massal
    protected $fillable = [
        'owner_id', 'name', 'slug', 'address', 'phone', 'logo', 'qris_image',
        'open_time', 'close_time', 'status', 'security_pin', 'qris_image', 'qris_owner_name', 
        'whatsapp_link', 'instagram_link', 'tiktok_link',
    ];

    /**
     * RELASI: Restaurant milik 1 User (owner)
     * Artinya: $restaurant->owner → balikin object User
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * RELASI: Restaurant punya banyak User (owner + staff)
     * Artinya: $restaurant->users → balikin collection User
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function tables()
    {
        return $this->hasMany(Table::class);
    }

}