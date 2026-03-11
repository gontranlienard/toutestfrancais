<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryMappingRule extends Model
{
    protected $fillable = [
        'site_id',
        'keyword',
        'category_id',
        'priority'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}

