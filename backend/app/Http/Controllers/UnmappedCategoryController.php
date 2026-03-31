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
        $query = UnmappedCategory::with(['site'])
            ->orderByDesc('occurrences');

        if ($request->filled('search')) {
            $query->where('raw_category', 'LIKE', '%' . $request->search . '%');
        }

        $unmapped = $query->paginate(20)->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | 🔥 ON TRANSFORME EN PRODUITS (IMPORTANT)
        |--------------------------------------------------------------------------
        */

        $productsList = collect();

        foreach ($unmapped as $item) {

            $products = DB::table('products')
                ->join('variants', 'variants.product_id', '=', 'products.id')
                ->join('offers', 'offers.variant_id', '=', 'variants.id')
                ->where('offers.site_id', $item->site_id)
                ->select('products.id', 'products.name')
                ->distinct()
                ->limit(5)
                ->get();

            foreach ($products as $product) {
                $product->raw_category = $item->raw_category;
                $product->site_id = $item->site_id;
                $productsList->push($product);
            }
        }

        $categories = Category::with('childrenRecursive')
            ->whereNull('parent_id')
            ->get();

        return view('admin.unmapped-categories', [
            'products' => $productsList,
            'categories' => $categories,
            'unmapped' => $unmapped // 🔥 IMPORTANT pour la pagination
        ]);
    }


    public function mapProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'category_id' => 'required|integer',
            'rule' => 'nullable|string',
            'priority' => 'required|integer'
        ]);

        DB::transaction(function () use ($request) {

            // 1️⃣ product → category
            DB::table('product_category')->updateOrInsert([
                'product_id' => $request->product_id,
                'category_id' => $request->category_id,
            ]);

            // 2️⃣ règle
            if (!empty($request->rule)) {

                DB::table('category_mapping_rules')->insert([
                    'keyword' => Str::lower(trim($request->rule)),
                    'category_id' => $request->category_id,
                    'priority' => $request->priority,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // 3️⃣ suppression unmapped
            DB::table('unmapped_categories')
                ->where('raw_category', $request->raw_category ?? null)
                ->delete();

        });

        return response()->json(['success' => true]);
    }


    public function rebuildCategories()
    {
        Artisan::call('products:link-categories');
        return back()->with('success', 'Catégories recalculées');
    }


    public function rules()
    {
        $rules = DB::table('category_mapping_rules')
            ->join('categories', 'categories.id', '=', 'category_mapping_rules.category_id')
            ->select(
                'category_mapping_rules.*',
                'category_mapping_rules.category_id as rule_category_id',
                'categories.name as category_name'
            )
            ->orderByDesc('category_mapping_rules.id')
            ->paginate(50);

        // 🔥 FIX : on force toutes les catégories
        $categories = DB::table('categories')->get();

        return view('admin.mapping-rules', [
            'rules' => $rules,
            'categories' => $categories
        ]);
    }


    public function updateRule(Request $request)
    {
        if (!$request->has('priority')) {
            return response()->json([
                'success' => false,
                'error' => 'priority missing',
                'data' => $request->all()
            ]);
        }

        $id = (int) $request->id;

        $updated = DB::table('category_mapping_rules')
            ->where('id', $id)
            ->update([
                'priority' => (int)$request->priority,
                'keyword' => strtolower(trim($request->keyword)),
                'category_id' => (int)$request->category_id,
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'updated_rows' => $updated,
            'received' => $request->all()
        ]);
    }


    public function deleteRule(Request $request)
    {
        $request->validate([
            'id' => 'required|integer'
        ]);

        DB::table('category_mapping_rules')
            ->where('id', $request->id)
            ->delete();

        return response()->json(['success' => true]);
    }


    public function deleteBulkRules(Request $request)
    {
        $request->validate([
            'ids' => 'required|array'
        ]);

        DB::table('category_mapping_rules')
            ->whereIn('id', $request->ids)
            ->delete();

        return response()->json(['success' => true]);
    }


    public function createRule(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string',
            'category_id' => 'required|integer',
            'priority' => 'required|integer'
        ]);

        DB::table('category_mapping_rules')->insert([
            'keyword' => strtolower(trim($request->keyword)),
            'category_id' => (int)$request->category_id,
            'priority' => (int)$request->priority,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['success' => true]);
    }
}