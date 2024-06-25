<?php

namespace App\Http\Middleware;
use App\Http\Responses\ApiResponses;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

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
        if (Auth::guard('sanctum')->check()) {
            return $next($request);
        }
        return ApiResponses::error("No autenticado",401);
    }
}
