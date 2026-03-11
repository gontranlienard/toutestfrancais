<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    protected $table = 'price_history';

    protected $fillable = [
        'offer_id',
        'price'
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}

