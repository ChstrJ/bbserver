<?php

namespace App\Http\Middleware;

use App\Http\Helpers\HttpStatusMessage;
use App\Http\Helpers\utils\Roles;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

       if(Auth::check() && Auth::user()->isAdmin()) {
        return $next($request);
       }

      return response()->json(HttpStatusMessage::$FORBIDDEN);
    }
}
