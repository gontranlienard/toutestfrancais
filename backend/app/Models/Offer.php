<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $table = 'offers';

    protected $fillable = [
        'product_id',
        'site_id',
        'price',
        'url'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
