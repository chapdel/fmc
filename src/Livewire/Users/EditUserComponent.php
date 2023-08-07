<?php

namespace Spatie\Mailcoach\Livewire\Users;

use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Settings\Models\User;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\LivewireFlash;

class EditUserComponent extends Component
{
    use UsesMailcoachModels;
    use LivewireFlash;

    public User $user;

    public ?string $email;

    public ?string $name;

    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc', Rule::unique(self::getUserTableName(), 'email')->ignore($this->user->id)],
            'name' => ['required', 'string'],
        ];
    }

    public function mount(User $mailcoachUser)
    {
        $this->user = $mailcoachUser;
        $this->fill($this->user->toArray());
    }

    public function save()
    {
        $this->user->update($this->validate());

        $this->flash(__mc('The user has been updated.'));
    }

    public function render()
    {
        return view('mailcoach::app.configuration.users.edit')
            ->layout('mailcoach::app.layouts.settings', ['title' => $this->user->name]);
    }
}
