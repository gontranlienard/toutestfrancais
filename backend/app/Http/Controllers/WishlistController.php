<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Variant;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::with('variant.product', 'variant.offers')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('account.wishlist', compact('wishlists'));
    }

    public function toggle($variantId)
    {

        if (!Auth::check()) {

            return response()->json([
                'error' => 'not_logged'
            ]);
        }

        $user = Auth::user();

        $exists = Wishlist::where('user_id', $user->id)
            ->where('variant_id', $variantId)
            ->first();

        // Si déjà en favoris → on supprime
        if ($exists) {

            $exists->delete();

            return response()->json([
                'status' => 'removed'
            ]);
        }

        // récupération variante
        $variant = Variant::findOrFail($variantId);

        // récupérer le prix le plus bas
        $price = $variant->offers()->min('price');

        Wishlist::create([
            'user_id' => $user->id,
            'variant_id' => $variantId,
            'price_when_added' => $price
        ]);

        return response()->json([
            'status' => 'added'
        ]);
    }   // ← fermeture de toggle manquante

    public function destroy($id)
    {
        $wishlist = Wishlist::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();

        $wishlist->delete();

        return redirect()->back();
    }
}