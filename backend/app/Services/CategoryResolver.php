<?php

namespace App\Services;

use App\Models\CategoryMappingRule;
use App\Models\SiteCategoryMapping;
use App\Models\UnmappedCategory;
use App\Models\Site;

class CategoryResolver
{
    public function resolve(string $rawCategory, Site $site)
    {
        $raw = strtolower($rawCategory);

        $rules = CategoryMappingRule::where(function ($q) use ($site) {
            $q->whereNull('site_id')
              ->orWhere('site_id', $site->id);
        })
        ->orderByDesc('priority')
        ->get();

        foreach ($rules as $rule) {
            if (str_contains($raw, strtolower($rule->keyword))) {
                return $rule->category_id;
            }
        }

        $manual = SiteCategoryMapping::where('site_id', $site->id)
            ->where('site_category_identifier', $rawCategory)
            ->first();

        if ($manual) {
            return $manual->category_id;
        }

        // 🔥 Log des non mappées
        UnmappedCategory::updateOrCreate(
            [
                'site_id' => $site->id,
                'raw_category' => $rawCategory
            ],
            []
        );

        return null;
    }
}
