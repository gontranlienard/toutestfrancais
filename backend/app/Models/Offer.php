<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $table = 'offers';

    protected $fillable = [
        'variant_id',
        'site_id',
        'price',
        'currency',
        'availability',
        'url'
    ];

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function priceHistory()
    {
        return $this->hasMany(PriceHistory::class);
    }
	public function product()
	{
    return $this->belongsTo(\App\Models\Product::class);
	}
}


