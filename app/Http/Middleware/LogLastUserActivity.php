<?php

namespace App\Http\Middleware;

use JWTAuth;
use Closure;
use Carbon\Carbon;
use Cache;

class LogLastUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($user = JWTAuth::parseToken()->authenticate()) {
            $expiresAt = Carbon::now()->addMinutes(5);
            Cache::put('user-is-online-' . $user->id, true, $expiresAt);
        }

        return $next($request);
    }
}
