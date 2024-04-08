<?php

namespace App\Http\Middleware;

use App\Http\Utils\HttpStatusMessage;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //get the req header
        $xApiKey = $request->header('x-api-key');

        //compare the req header if its equal to the env api key
        if($xApiKey !== env('X_API_KEY')) {
            return response()->json(HttpStatusMessage::$WRONG_KEY , 403);
        }
        return $next($request);
    }
}
