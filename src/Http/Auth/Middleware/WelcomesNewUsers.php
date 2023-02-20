<?php

namespace Spatie\Mailcoach\Http\Auth\Middleware;

use Carbon\Carbon;
use Closure;
use Symfony\Component\HttpFoundation\Response;

class WelcomesNewUsers
{
    public function handle($request, Closure $next)
    {
        if (! $request->hasValidSignature()) {
            abort(Response::HTTP_FORBIDDEN, __('The welcome link does not have a valid signature or is expired.'));
        }

        if (! $request->mailcoachUser) {
            abort(Response::HTTP_FORBIDDEN, __('Could not find a user to be welcomed.'));
        }

        if (is_null($request->mailcoachUser->welcome_valid_until)) {
            abort(Response::HTTP_FORBIDDEN, __('The welcome link has already been used.'));
        }

        if (Carbon::create($request->mailcoachUser->welcome_valid_until)->isPast()) {
            abort(Response::HTTP_FORBIDDEN, __('The welcome link has expired.'));
        }

        return $next($request);
    }
}
