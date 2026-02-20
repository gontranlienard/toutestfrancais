<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
}
