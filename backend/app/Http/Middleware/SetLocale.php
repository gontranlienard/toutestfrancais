<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        $locale = $request->get('lang', Session::get('locale', 'fr'));

        if (!in_array($locale, ['fr', 'en'])) {
            $locale = 'fr';
        }

        App::setLocale($locale);
        Session::put('locale', $locale);

        return $next($request);
    }
}
