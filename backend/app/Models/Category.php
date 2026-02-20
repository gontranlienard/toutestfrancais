<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'parent_id'
    ];

    public function products()
    {
        return $this->belongsToMany(
            Product::class,
            'category_product',
            'category_id',
            'product_id'
        );
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')
                    ->with('children');
    }
}
