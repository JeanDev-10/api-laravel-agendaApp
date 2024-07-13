<?php

namespace App\Http\Middleware;
use App\Http\Responses\ApiResponses;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
    public function handle($request, Closure $next,...$args)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return ApiResponses::error('No autenticado', 401);
            }
        } catch (TokenExpiredException $e) {
            return ApiResponses::error('Token expirado', 401);
        } catch (TokenInvalidException $e) {
            return ApiResponses::error('Token invalido', 401);
        } catch (JWTException $e) {
            return ApiResponses::error('No autenticado', 401);
        }
        
        return $next($request);
    }
}
