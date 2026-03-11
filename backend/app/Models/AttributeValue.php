<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    protected $table = 'attribute_values';

    protected $fillable = [
        'attribute_id',
        'value',
        'slug'
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function variants()
    {
        return $this->belongsToMany(
            Variant::class,
            'variant_attribute_values',
            'attribute_value_id',
            'variant_id'
        );
    }
}
