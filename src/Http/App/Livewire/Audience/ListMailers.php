<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\View\View;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\Http\App\Requests\EmailLists\Settings\UpdateEmailListGeneralSettingsRequest;

class ListMailers extends Component
{
    use UsesMailcoachModels;
    use LivewireFlash;

    public EmailList $emailList;

    protected function rules(): array
    {
        return [
            'emailList.campaign_mailer' => [Rule::in(array_keys(config('mail.mailers')))],
            'emailList.automation_mailer' => [Rule::in(array_keys(config('mail.mailers')))],
            'emailList.transactional_mailer' => [Rule::in(array_keys(config('mail.mailers')))],
        ];
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
        return view('mailcoach::app.emailLists.settings.mailers')
            ->layout('mailcoach::app.emailLists.layouts.emailList', [
                'title' => __('mailcoach - Mailers'),
                'emailList' => $this->emailList,
            ]);
    }
}
