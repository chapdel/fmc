<?php

namespace Spatie\Mailcoach\Livewire\Users;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Settings\Models\User;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class CreateUserComponent extends Component
{
    use UsesMailcoachModels;

    public string $email = '';

    public string $name = '';

    public function saveUser()
    {
        $validated = $this->validate([
            'email' => ['required', 'email:rfc', Rule::unique('users', 'email')],
            'name' => 'required|string',
        ]);

        /** @var User $user */
        $user = self::getUserClass()::create(array_merge($validated, ['password' => bcrypt(Str::random(64))]));

        $expiresAt = now()->addDay();

        try {
            $user->sendWelcomeNotification($expiresAt);

            flash()->success(__mc('The user has been created. A mail with login instructions has been sent to :email', ['email' => $user->email]));
        } catch (\Throwable $e) {
            report($e);
            flash()->error(__mc('The user has been created. A mail with setup instructions could not be sent: '.$e->getMessage()));
        }

        return redirect()->route('users');
    }

    public function render()
    {
        return view('mailcoach::app.configuration.users.partials.create');
    }
}
