<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'brand_id',
        'name',
        'slug',
        'image',
        'normalized_name',
        'model_key',
        'site_category_path'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {

            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }

            if (empty($product->normalized_name)) {
                $product->normalized_name = Str::lower(
                    preg_replace('/[^a-zA-Z0-9]/', '', $product->name)
                );
            }
        });

        /*
        |--------------------------------------------------------------------------
        | Charger automatiquement le prix minimum
        |--------------------------------------------------------------------------
        */

        static::addGlobalScope('min_price', function ($query) {
            $query->withMin('offers', 'price');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }

    // 🔥 IMPORTANT : OFFERS via variants
    public function offers()
    {
        return $this->hasManyThrough(
            Offer::class,
            Variant::class,
            'product_id',   // FK sur variants
            'variant_id',   // FK sur offers
            'id',           // PK sur products
            'id'            // PK sur variants
        );
    }

    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'product_category',
            'product_id',
            'category_id'
        );
    }
}