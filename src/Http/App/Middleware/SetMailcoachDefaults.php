<?php

namespace Spatie\Mailcoach\Http\App\Middleware;

use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Auth\Notifications\ResetPassword;
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

        FilamentColor::register([
            'danger' => Color::Red,
            'gray' => Color::Zinc,
            'info' => Color::Blue,
            'primary' => Color::Blue,
            'success' => Color::Green,
            'warning' => Color::Amber,
        ]);

        Notification::configureUsing(function (Notification $notification): void {
            $notification->view('mailcoach::app.layouts.partials.flash');
        });

        return $next($request);
    }
}
