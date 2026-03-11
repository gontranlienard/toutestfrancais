<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteCategory extends Model
{
    protected $table = 'site_category_mappings';
	protected $fillable = [
    'site_id',
    'site_category_identifier',
    'site_category_name',
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
	public function products()
	{
    return $this->hasMany(\App\Models\Product::class, 'site_category_id');
	}
	public function site()
	{
    return $this->belongsTo(Site::class);
	}
}
