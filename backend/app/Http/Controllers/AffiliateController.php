<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Support\Facades\DB;

class AffiliateController extends Controller
{
    public function redirect($offerId)
    {
        $offer = Offer::with('site','product')->findOrFail($offerId);

        $url = $offer->affiliate_url;

        DB::table('affiliate_clicks')->insert([
            'product_id' => $offer->product_id,
            'offer_id' => $offer->id,
            'site_id' => $offer->site_id,
            'clicked_url' => $url,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referer' => request()->headers->get('referer'),
            'session_id' => session()->getId(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->away($url);
    }
}