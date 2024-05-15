<?php

namespace App\Http\Middleware;

use App\Http\Utils\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EmployeeRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user()->role_id;
        if (Auth::check() && ($user === Role::$ADMIN || $user === Role::$EMPLOYEE)) {
            return $next($request);
        }

        return abort(403, HttpStatusMessage::$FORBIDDEN);
    }
}
