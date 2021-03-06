<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;

class UserProfile extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $roles, $permissions, $validateAll = false)
    {

        if (! $token = $this->auth->setRequest($request)->getToken()) {
          return $this->respond('tymon.jwt.absent', 'token_not_provided', 400);
        }

        try {
          $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
          return $this->respond('tymon.jwt.expired', 'token_expired', $e->getStatusCode(), [$e]);
        } catch (JWTException $e) {
          return $this->respond('tymon.jwt.invalid', 'token_invalid', $e->getStatusCode(), [$e]);
        }

        if (! $user) {
          return $this->respond('tymon.jwt.user_not_found', 'user_not_found', 404);
        }

        // The the id of the User that is to be edited matches the id of the authorized User, then return early.        
        if($request->id == $user->id){
            return $next($request);
        }

        // If the User doesn't have sufficient Role/Permissions to access then return 401.
        if (!$request->user()->ability(explode('|', $roles), explode('|', $permissions), array('validate_all' => $validateAll))) {
          return $this->respond('tymon.jwt.invalid', 'token_invalid_permissions', 401, 'Unauthorized');
        }

        return $next($request);
    }
}
