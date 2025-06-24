<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'slug', 
        'image', 
        'price', 
        'discount_price', 
        'description', 
        'category_id', 
        'brand_id', 
        'is_active', 
        'stock', 
        'sku', 
    ];

    public function categorie():BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    public function brand():BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function productPhotos():HasMany
    {
        return $this->hasMany(ProductPhoto::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
}
