<?php

namespace Spatie\Mailcoach\Http\Auth\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class LoginController
{
    use AuthenticatesUsers;
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    protected string $redirectTo;

    public function __construct()
    {
        $this->redirectTo = route(config('mailcoach.redirect_home') ?? 'mailcoach.dashboard');
    }

    public function showLoginForm()
    {
        return view('mailcoach::auth.login');
    }
}
