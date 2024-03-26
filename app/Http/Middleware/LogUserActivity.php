<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {


        if (Auth::check()) {
            $user = Auth::user();
            dd($user);
            if ($request->is('/auth/login')) {
                $user->last_login_at = Carbon::now();
                $user->save();
            } else if ($request->is('/auth/logout')) {
                $user->last_logout_at = Carbon::now();
                $user->save();
            }
        }
        return $next($request);
    }
}
