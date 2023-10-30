<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\MainNavigation;

class ListMailersComponent extends Component
{
    use UsesMailcoachModels;

    public EmailList $emailList;

    public ?string $campaign_mailer;

    public ?string $automation_mailer;

    public ?string $transactional_mailer;

    protected function rules(): array
    {
        return [
            'campaign_mailer' => ['nullable', Rule::in(array_keys(config('mail.mailers')))],
            'automation_mailer' => ['nullable', Rule::in(array_keys(config('mail.mailers')))],
            'transactional_mailer' => ['nullable', Rule::in(array_keys(config('mail.mailers')))],
        ];
    }

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;
        $this->fill($emailList->toArray());

        app(MainNavigation::class)->activeSection()->add($this->emailList->name, route('mailcoach.emailLists.mailers', $this->emailList));
    }

    public function save()
    {
        $this->validate();

        $this->emailList->fill(Arr::except($this->all(), 'emailList'));
        $this->emailList->save();

        notify(__mc('List :emailList was updated', ['emailList' => $this->emailList->name]));
    }

    public function render(): View
    {
        return view('mailcoach::app.emailLists.settings.mailers')
            ->layout('mailcoach::app.emailLists.layouts.emailList', [
                'title' => __mc('Mailers'),
                'emailList' => $this->emailList,
            ]);
    }
}
