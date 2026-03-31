<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Affiliate extends Model
{
    protected $fillable = [
        'site_id',
        'name',
        'platform',
        'url_template',
        'commission_percent',
        'cookie_days',
        'active'
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}