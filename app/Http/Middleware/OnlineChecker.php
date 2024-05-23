<?php

namespace App\Http\Middleware;

use App\Http\Helpers\user\UserStatus;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OnlineChecker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            $lastActivity = new Carbon($user->last_activity);
            if ($lastActivity->diffInMinutes(now()) <= 15) {
                $user->last_activity = now();
                $user->status = UserStatus::$ONLINE;
            } else {
                $user->status = UserStatus::$OFFLINE;
            }
            $user->save();
        }

        return $next($request);
    }
}
