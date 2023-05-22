<?php
namespace App\Http\Middleware;

use Closure;

class SetHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');
        $response->headers->set('Access-Control-Allow-Headers', 'X-Requested-With,content-type,Authorization');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        return $response;
    }
}
