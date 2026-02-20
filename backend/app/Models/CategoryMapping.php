<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryMapping extends Model
{
    protected $fillable = [
        'site_category_id',
        'category_id'
    ];

    public function siteCategory()
    {
        return $this->belongsTo(SiteCategory::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
