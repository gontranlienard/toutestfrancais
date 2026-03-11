<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    protected $fillable = [
        'site_slug',
        'filename',
        'status',
        'total_products',
        'processed_products',
        'success_products',
        'failed_products',
        'errors',
        'started_at',
        'finished_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime'
    ];
}
