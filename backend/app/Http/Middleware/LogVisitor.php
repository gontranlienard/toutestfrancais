<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogVisitor
{
    public function handle(Request $request, Closure $next)
    {	
		
        // 🔥 IP à exclure
		$excludedIps = [
			'37.66.41.54', // ton IP
		];
		if (in_array($request->ip(), $excludedIps)) {
			return $next($request);
		}
		
		$response = $next($request);

        // ❌ on ignore les assets
        if ($request->is('css/*') || $request->is('js/*') || $request->is('images/*')) {
            return $response;
        }

        DB::table('visitor_logs')->insert([
            'session_id' => session()->getId(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'referrer' => $request->headers->get('referer'),
            'event_type' => 'pageview',
            'event_value' => null,
            'created_at' => now(),
        ]);

        return $response;
    }
}