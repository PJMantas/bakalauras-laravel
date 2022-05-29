<?php
namespace App\Http\Middleware;
use Illuminate\Http\Request;
use Closure;
class CORS {
    
    public function handle(Request $request, Closure $next) {
        header('Acess-Control-Allow-Origin: *');
        header('Acess-Control-Allow-Origin: Content-type, X-Auth-Token, Authorization, Origin');
        //header('Access-Control-Allow-Origin: *');
        //header('Access-Control-Allow-Methods: *');
        //header('Access-Control-Allow-Headers: *');
        return $next($request);
    }
}