<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'image_url'
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}