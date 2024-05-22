<?php

namespace App\Http\Middleware;

use App\Http\Utils\HttpStatusMessage;
use App\Http\Utils\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check() && Auth::user()->role_id === Role::SUPER_ADMIN) {
            return $next($request);
        }
        return abort(403, HttpStatusMessage::$FORBIDDEN);
    }
}
