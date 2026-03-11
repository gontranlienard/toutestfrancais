<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    protected $table = 'variants';

    protected $fillable = [
        'product_id',
        'ean',
        'sku',
        'normalized_variant'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(
            AttributeValue::class,
            'variant_attribute_values',
            'variant_id',
            'attribute_value_id'
        );
    }
}


