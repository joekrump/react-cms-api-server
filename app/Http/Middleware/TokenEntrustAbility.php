<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Dingo\Api\Routing\Helpers;

class TokenEntrustAbility extends BaseMiddleware
{
  use Helpers;

  public function handle($request, Closure $next, $roles, $permissions, $validateAll = false)
  {

    if (! $token = $this->auth->setRequest($request)->getToken()) {
      return $this->respond('tymon.jwt.absent', 'token_not_provided', 400);
    }

    try {
      $user = $this->auth->authenticate($token);
    } catch (TokenExpiredException $e) {
      return $this->response->error('Token Expired', $e->getStatusCode());
    } catch (JWTException $e) {
      return $this->response->error('Token Invalid', $e->getStatusCode());
    } catch (Exception $e) {
      return $this->response->error('Something funny happened', 500);
    }

    if (! $user) {
      return $this->response->error('User Not Found', 404);
    }

    if (!$request->user()->ability(explode('|', $roles), explode('|', $permissions), array('validate_all' => $validateAll))) {
      return $this->response->error('Invalid Token Permissions', 401);
    }

    $this->events->fire('tymon.jwt.valid', $user);

    return $next($request);
  }
}