<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteCategoryMapping extends Model
{
    protected $fillable = [
        'site_id',
        'site_category_identifier',
        'site_category_name',
        'category_id'
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
