<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'position' // 🔥 IMPORTANT
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function getFullSlug()
    {
        $slugs = [];
        $category = $this;

        while ($category) {
            array_unshift($slugs, $category->slug);
            $category = $category->parent;
        }

        return implode('/', $slugs);
    }

    // 🔥 TRI ICI
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')
                    ->orderBy('position');
    }

    public function childrenRecursive()
    {
        return $this->children()
                    ->withCount('products')
                    ->with('childrenRecursive');
    }

    public function products()
    {
        return $this->belongsToMany(
            \App\Models\Product::class,
            'product_category',
            'category_id',
            'product_id'
        );
    }
}