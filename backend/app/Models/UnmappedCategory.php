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
	public function example_product()
	{
    return $this->hasOneThrough(
        Product::class,
        Offer::class,
        'site_id',     // clé sur offers
        'id',          // clé sur products
        'site_id',     // clé sur unmapped_categories
        'product_id'   // clé sur offers
    );
	}
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

}