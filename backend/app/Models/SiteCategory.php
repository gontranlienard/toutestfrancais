<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteCategory extends Model
{
    protected $fillable = [
        'site_id',
        'name',
        'slug',
        'parent_id',
        'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
}
