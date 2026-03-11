<?php

namespace App\Services;

use App\Models\Category;

class CategoryMatcher
{
    public function suggest(string $siteCategoryIdentifier)
    {
        $slug = strtolower($siteCategoryIdentifier);

        $categories = Category::all();

        $bestMatch = null;
        $bestScore = 0;

        foreach ($categories as $category) {

            $score = 0;

            $categoryWords = explode(' ', strtolower($category->name));
            $slugWords = preg_split('/[-\/]/', $slug);

            foreach ($categoryWords as $word) {
                if (in_array($word, $slugWords)) {
                    $score++;
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $category;
            }
        }

        return $bestScore > 0 ? $bestMatch : null;
    }
}