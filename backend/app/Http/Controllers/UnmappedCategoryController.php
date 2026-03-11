<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\UnmappedCategory;
use Illuminate\Support\Facades\Artisan;

class UnmappedCategoryController extends Controller
{

    public function index(Request $request)
    {

        $query = UnmappedCategory::with('site')
            ->orderByDesc('occurrences');

        if ($request->filled('search')) {

            $query->where('raw_category', 'LIKE', '%' . $request->search . '%');

        }

        $unmapped = $query->paginate(50);

        $categories = Category::with('childrenRecursive')
            ->whereNull('parent_id')
            ->get();

        return view('admin.unmapped-categories',[
            'unmapped' => $unmapped,
            'categories' => $categories,
            'search' => $request->search
        ]);

    }



    public function mapBulk(Request $request)
    {

        $request->validate([
            'raw_categories' => 'required|array',
            'category_id' => 'required|integer'
        ]);


        foreach ($request->raw_categories as $rawCategory) {

            $keyword = Str::lower(trim($rawCategory));


            $exists = DB::table('category_mapping_rules')
                ->whereNull('site_id')
                ->where('keyword',$keyword)
                ->exists();


            if (!$exists) {

                DB::table('category_mapping_rules')->insert([
                    'site_id' => null,
                    'keyword' => $keyword,
                    'category_id' => $request->category_id,
                    'priority' => 100,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

            }


            DB::table('unmapped_categories')
                ->where('raw_category',$rawCategory)
                ->delete();
        }


        return back()->with('success','Catégories mappées');

    }
	public function rebuildCategories()
{
    Artisan::call('products:link-categories');

    return back()->with('success','Catégories recalculées');
}

}