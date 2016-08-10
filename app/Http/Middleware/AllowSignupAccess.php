<?php

namespace App\Http\Middleware;

use Closure;
use App\User;


class AllowSignupAccess
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
        if(User::all()->count() > 0) {
            return $this->respond('Access to Signup no allowed', 'Signup allowed only if no User present', 401);
        }
        return $next($request);
    }
}
