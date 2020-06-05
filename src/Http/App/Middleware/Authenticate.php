<?php

namespace Spatie\Mailcoach\Http\App\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticationMiddleware;

class Authenticate extends BaseAuthenticationMiddleware
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  string[]  ...$guards
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        return $next($request);

    }
}
