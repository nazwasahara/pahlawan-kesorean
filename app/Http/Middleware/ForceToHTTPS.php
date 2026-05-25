<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\URL;

class ForceToHTTPS
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // ngrok

        if (str_ends_with(request()->getHost(), '.ngrok-free.app')) {
            if (!$request->isSecure()) {
                URL::forceScheme('https');
            }
        }

        return $next($request);
    }
}
