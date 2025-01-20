<?php 

namespace App\Http\Middleware;

use Illuminate\Http\Request;

class EnsureJson
{
    public function handle(Request $request, \Closure $next)
    {
        $request->headers->set('Accept', 'application/json', true);
        $request->headers->set('Content-Type', 'application/json', true);

        return $next($request);
    }
}