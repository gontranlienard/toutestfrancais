<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnmappedCategory extends Model
{
    protected $table = 'unmapped_categories';

    protected $fillable = [
        'site_id',
        'raw_category',
        'occurrences'
    ];

    protected $casts = [
        'occurrences' => 'integer'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

}