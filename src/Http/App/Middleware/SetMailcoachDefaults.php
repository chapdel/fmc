<?php

namespace Spatie\Mailcoach\Http\App\Middleware;

use Filament\Tables\Table;
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

        Table::configureUsing(function (Table $table): void {
            $table
                ->paginationPageOptions([10, 25, 50, 100])
                ->deferLoading();
        });

        return $next($request);
    }
}
