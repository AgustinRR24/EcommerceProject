<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPhoto extends Model
{
    use HasFactory;

    protected $table = 'product_photos';

    protected $fillable = [
        'product_id',
        'url_photo'
    ];

    public function product():BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
