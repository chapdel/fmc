<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Carbon\CarbonInterval;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\HaltAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\SendAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedTrigger;
use Spatie\Mailcoach\Domain\Campaign\Mails\WelcomeMail;
use Spatie\Mailcoach\Domain\Shared\Support\TemplateRenderer;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\MainNavigation;

class ListOnboarding extends Component
{
    use UsesMailcoachModels;
    use LivewireFlash;

    public const CONFIRMATION_MAIL_DEFAULT = 'send_default_confirmation_mail';
    public const CONFIRMATION_MAIL_CUSTOM = 'send_custom_confirmation_mail';

    public EmailList $emailList;

    public string $confirmation_mail;

    public array $allowed_form_subscription_tags;

    protected $listeners = [
        'tags-updated-allowed_form_subscription_tags' => 'updateAllowedFormSubscriptionTags',
    ];

    protected function rules(): array
    {
        return [
            'emailList.allow_form_subscriptions' => 'boolean',
            'emailList.allowed_form_extra_attributes' => '',
            'emailList.requires_confirmation' => 'boolean',
            'allowed_form_subscription_tags' => 'array',
            'emailList.redirect_after_subscribed' => '',
            'emailList.redirect_after_already_subscribed' => '',
            'emailList.redirect_after_subscription_pending' => '',
            'emailList.redirect_after_unsubscribed' => '',
            'confirmation_mail' => Rule::in([static::CONFIRMATION_MAIL_DEFAULT, static::CONFIRMATION_MAIL_CUSTOM]),
            'emailList.confirmation_mail_subject' => 'required_if:confirmation_mail,' . static::CONFIRMATION_MAIL_CUSTOM,
            'emailList.confirmation_mail_content' => 'required_if:confirmation_mail,'. static::CONFIRMATION_MAIL_CUSTOM,
        ];
    }

    protected function messages()
    {
        $customMailRequiredValidationMessage = __('mailcoach - This field is required when using a custom mail');

        return [
            'emailList.confirmation_mail_subject.required_if' => $customMailRequiredValidationMessage,
            'emailList.confirmation_mail_content.required_if' => $customMailRequiredValidationMessage,
        ];
    }

    public function updateAllowedFormSubscriptionTags(array $tags)
    {
        $this->allowed_form_subscription_tags = $tags;
    }

    public function updatedConfirmationMail()
    {
        if ($this->confirmation_mail === self::CONFIRMATION_MAIL_DEFAULT) {
            $this->emailList->confirmation_mail_subject = null;
            $this->emailList->confirmation_mail_content = null;
        }
    }

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList->load(['tags', 'allowedFormSubscriptionTags']);
        $this->allowed_form_subscription_tags = $this->emailList->allowedFormSubscriptionTags->pluck('name')->toArray();

        app(MainNavigation::class)->activeSection()->add($this->emailList->name, route('mailcoach.emailLists.onboarding', $this->emailList));
    }

    public function save()
    {
        $this->validate();

        $this->emailList->save();
        $this->emailList->allowedFormSubscriptionTags()->sync(self::getTagClass()::whereIn('name', $this->allowed_form_subscription_tags)->pluck('id'));

        $this->flash(__('mailcoach - List :emailList was updated', ['emailList' => $this->emailList->name]));
    }

    public function render(): View
    {
        $this->confirmation_mail = $this->emailList->hasCustomizedConfirmationMailFields()
            ? self::CONFIRMATION_MAIL_CUSTOM
            : self::CONFIRMATION_MAIL_DEFAULT;

        return view('mailcoach::app.emailLists.settings.onboarding')
            ->layout('mailcoach::app.emailLists.layouts.emailList', [
                'title' => __('mailcoach - Onboarding'),
                'emailList' => $this->emailList,
            ]);
    }
}
