<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\Http\App\Requests\EmailLists\Settings\UpdateEmailListGeneralSettingsRequest;

class ListSettings extends Component
{
    use UsesMailcoachModels;
    use LivewireFlash;

    public EmailList $emailList;

    protected function rules(): array
    {
        return collect((new UpdateEmailListGeneralSettingsRequest)->rules())
            ->mapWithKeys(fn ($value, string $key) => ["emailList.{$key}" => $value])
            ->toArray();
    }

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;
    }

    public function save()
    {
        $this->validate();

        $this->emailList->save();

        $this->flash(__('mailcoach - List :emailList was updated', ['emailList' => $this->emailList->name]));
    }

    public function render(): View
    {
        return view('mailcoach::app.emailLists.settings.general')
            ->layout('mailcoach::app.emailLists.layouts.emailList', [
                'title' => __('mailcoach - General'),
                'emailList' => $this->emailList,
            ]);
    }
}
