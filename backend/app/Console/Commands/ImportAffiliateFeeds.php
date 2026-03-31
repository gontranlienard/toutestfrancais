<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Offer;
use App\Models\PriceHistory;
use App\Models\Site;

class ImportAffiliateFeeds extends Command
{
    protected $signature = 'import:affiliate-feeds';
    protected $description = 'Import affiliate feeds (Effinity XML)';

    private $colors = [
        'noir','black','blanc','white','rouge','red','bleu','blue',
        'vert','green','jaune','yellow','orange','gris','grey','gray',
        'marron','brown','rose','pink','violet','purple','argent','silver',
        'or','gold','beige','fluo','transparent'
    ];

    public function handle()
    {
        $feeds = DB::table('affiliate_feeds')
            ->where('active',1)
            ->get();

        foreach ($feeds as $feed) {

            $this->info("Import : ".$feed->name);

            $site = Site::find($feed->site_id);

            if(!$site){
                $this->error("Site introuvable");
                continue;
            }

            $tempFile = storage_path('app/feed.xml');

            $fp = fopen($tempFile, 'w');

            $ch = curl_init($feed->url);

            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 0);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');

            curl_exec($ch);

            if (curl_errno($ch)) {
                $this->error("Erreur CURL : ".curl_error($ch));
                curl_close($ch);
                fclose($fp);
                continue;
            }

            curl_close($ch);
            fclose($fp);

            $xml = simplexml_load_file($tempFile);

            if(!$xml){
                $this->error("XML invalide");
                continue;
            }

            $count = 0;

            foreach ($xml->product as $item) {

                $brandName = trim((string)$item->marque);
                $originalTitle = $this->fixUtf8(trim((string)$item->titre));
                $title = $originalTitle;
                $price = (float)$item->prix;

                if(!$title || !$price){
                    continue;
                }

                $size = $this->fixUtf8(trim((string)$item->taille));
                $color = $this->fixUtf8(trim((string)$item->couleur));
                $categoryPath = $this->fixUtf8(trim((string)$item->categorie));
				
				$eanRaw = trim((string)$item->ean);
                $ean = preg_replace('/[^0-9]/', '', $eanRaw);
				if ($ean === '' || $ean === '0') {
						$ean = null;
					}

				// ❌ mauvais format
				if ($ean && !in_array(strlen($ean), [12, 13])) {
					$ean = null;
				}

				// 🔥 faux EAN (zéros)
				if ($ean && preg_match('/^0+$/', $ean)) {
					$ean = null;
				}

				if ($ean && preg_match('/^0{4,}/', $ean)) {
					$ean = null;
				}
                $image = trim((string)$item->image);
                $trackingUrl = (string)$item->url;
                $mpn = trim((string)$item->mpn);

                parse_str(parse_url($trackingUrl, PHP_URL_QUERY), $params);
                $productUrl = urldecode($params['url'] ?? $trackingUrl);

                /*
                |--------------------------------------------------------------------------
                | 🔴 PAS D’EAN → TABLE À PART
                |--------------------------------------------------------------------------
                */

                if(!$ean){

                    DB::table('produits_sans_ean')->insert([
                        'titre' => $title,
                        'marque' => $brandName,
                        'prix' => $price,
                        'url' => $productUrl,
                        'image' => $image,
                        'site_id' => $site->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | MARQUE
                |--------------------------------------------------------------------------
                */

                $normalizedBrand = str_replace('-', '', Str::slug($brandName));

                $brand = Brand::where('normalized_name', $normalizedBrand)->first();

                if(!$brand){
                    $brand = Brand::create([
                        'name' => $brandName,
                        'slug' => Str::slug($brandName),
                        'normalized_name' => $normalizedBrand
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | 🔥 EAN FIRST
                |--------------------------------------------------------------------------
                */

                $variant = Variant::where('ean', $ean)->first();

                if($variant){

                    $product = Product::find($variant->product_id);

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | PRODUIT
                    |--------------------------------------------------------------------------
                    */

                    $normalizedName = $this->normalize($title);
                    $modelKey = $this->generateModelKey($brandName,$title);

                    $product = Product::firstOrCreate(
						[
							'brand_id' => $brand->id,
							'model_key' => $modelKey
						],
						[
							'name'=>$title,
							'slug'=>$this->uniqueSlug($title),
							'image'=>$image,
							'normalized_name'=>$normalizedName,
							'mpn'=>$mpn ?: null,
							'site_category_path'=>$categoryPath ?: null
						]
					);

                    /*
                    |--------------------------------------------------------------------------
                    | VARIANTE
                    |--------------------------------------------------------------------------
                    */

                    $variant = Variant::create([
                        'product_id'=>$product->id,
                        'ean'=>$ean,
                        'normalized_variant'=>$this->normalize($ean),
                        'size'=>$size ?: null,
                        'color'=>$color ?: null
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | OFFER
                |--------------------------------------------------------------------------
                */

                $offer = Offer::updateOrCreate(
                    [
                        'variant_id'=>$variant->id,
                        'site_id'=>$site->id
                    ],
                    [
                        'price'=>$price,
                        'currency'=>'EUR',
                        'availability'=>true,
                        'url'=>$productUrl
                    ]
                );

                PriceHistory::firstOrCreate(
                    [
                        'offer_id'=>$offer->id,
                        'price'=>$price
                    ]
                );

                $count++;
            }

            $this->info("Total import : ".$count);
        }

        $this->info("Import terminé");
    }

    /*
    |--------------------------------------------------------------------------
    | UTILS
    |--------------------------------------------------------------------------
    */

    private function fixUtf8($text)
    {
        if(!$text) return $text;

        $text = trim($text);

        if (preg_match('/Ã.|Â.|�/u', $text)) {
            $text = utf8_decode($text);
        }

        $text = mb_convert_encoding(
            $text,
            'UTF-8',
            'UTF-8, ISO-8859-1, WINDOWS-1252'
        );

        return iconv('UTF-8', 'UTF-8//IGNORE', $text);
    }

    private function normalize($value)
    {
        return Str::lower(preg_replace('/[^a-zA-Z0-9]/', '', $value));
    }

    private function generateModelKey($brand,$name)
    {
        return $this->normalize($brand.' '.$name);
    }

    private function uniqueSlug($name)
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while(Product::where('slug',$slug)->exists()){
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}