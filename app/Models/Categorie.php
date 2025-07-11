<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categorie extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'slug', 
        'icon', 
        'is_active'];

    public function products():HasMany
    {
        return $this->hasMany(Product::class);
    }
}
