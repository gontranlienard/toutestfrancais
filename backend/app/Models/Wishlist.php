<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $fillable = [
        'user_id',
        'variant_id',
        'price_when_added',
        'alert_sent_at'
    ];
}
