<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        $username = env('ADMIN_USER');
        $password = env('ADMIN_PASS');

        if (
            !isset($_SERVER['PHP_AUTH_USER']) ||
            !isset($_SERVER['PHP_AUTH_PW']) ||
            $_SERVER['PHP_AUTH_USER'] !== $username ||
            $_SERVER['PHP_AUTH_PW'] !== $password
        ) {
            header('WWW-Authenticate: Basic realm="Admin Area"');
            header('HTTP/1.0 401 Unauthorized');
            exit('Accès refusé');
        }

        return $next($request);
    }
}

