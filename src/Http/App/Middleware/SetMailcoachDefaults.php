<?php

namespace Spatie\Mailcoach\Http\App\Middleware;

use Illuminate\Auth\Notifications\ResetPassword;
use Livewire\Mechanisms\FrontendAssets\FrontendAssets;
use Spatie\Flash\Flash;

class SetMailcoachDefaults
{
    public function handle($request, $next)
    {
        Flash::levels([
            'success' => 'success',
            'warning' => 'warning',
            'error' => 'error',
        ]);

        if (config('mailcoach.guard')) {
            config()->set('auth.defaults.guard', config('mailcoach.guard'));
        }

        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            return url(route('mailcoach.password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        });

        app(FrontendAssets::class)->scriptTagAttributes = ['defer' => 'defer'];

        return $next($request);
    }
}
