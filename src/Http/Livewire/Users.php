<?php

namespace Spatie\Mailcoach\Http\Livewire;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Mailcoach\Domain\Settings\Models\User;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\Http\App\Queries\UsersQuery;

class Users extends DataTable
{
    use LivewireFlash;

    public function getTitle(): string
    {
        return __('Users');
    }

    public function getView(): string
    {
        return 'mailcoach::app.configuration.users.index';
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.layouts.settings';
    }

    public function getLayoutData(): array
    {
        return [
            'title' => __('Users'),
        ];
    }

    public function deleteUser(int $id)
    {
        if ($id === Auth::user()->id) {
            $this->flashError(__('You cannot delete yourself!'));

            return;
        }

        $user = User::find($id);
        $user->delete();

        $this->flash(__('The user has been deleted.'));
    }

    public function getData(Request $request): array
    {
        return [
            'users' => (new UsersQuery($request))->paginate(),
            'totalUsersCount' => User::count(),
        ];
    }
}
