<?php

namespace Spatie\Mailcoach\Livewire\Audience\Forms;

use Illuminate\Support\Arr;
use Livewire\Attributes\Rule;
use Livewire\Form;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class ListOnboardingForm extends Form
{
    use UsesMailcoachModels;

    public const CONFIRMATION_MAIL_DEFAULT = 'send_default_confirmation_mail';

    public const CONFIRMATION_MAIL_CUSTOM = 'send_custom_confirmation_mail';

    public EmailList $emailList;

    #[Rule(['boolean'])]
    public ?bool $allow_form_subscriptions = false;

    #[Rule(['nullable'])]
    public ?string $allowed_form_extra_attributes = '';

    #[Rule(['nullable', 'string'])]
    public ?string $honeypot_field = '';

    #[Rule(['boolean'])]
    public ?bool $requires_confirmation;

    #[Rule(['nullable', 'string'])]
    public ?string $redirect_after_subscribed = '';

    #[Rule(['nullable', 'string'])]
    public ?string $redirect_after_already_subscribed = '';

    #[Rule(['nullable', 'string'])]
    public ?string $redirect_after_subscription_pending = '';

    #[Rule(['nullable', 'string'])]
    public ?string $redirect_after_unsubscribed = '';

    public ?int $confirmation_mail_id = null;

    #[Rule(['array'])]
    public ?array $allowed_form_subscription_tags;

    #[Rule(['in:'.self::CONFIRMATION_MAIL_DEFAULT.','.self::CONFIRMATION_MAIL_CUSTOM])]
    public ?string $confirmation_mail = '';

    public function rules(): array
    {
        return [
            'confirmation_mail_id' => [
                'nullable',
                'required_if:form.confirmation_mail,'.self::CONFIRMATION_MAIL_CUSTOM,
                \Illuminate\Validation\Rule::exists(self::getTransactionalMailTableName(), 'id'),
            ],
        ];
    }

    public function setEmailList(EmailList $emailList): void
    {
        $this->emailList = $emailList;
        $this->allow_form_subscriptions = $this->emailList->allow_form_subscriptions;
        $this->allowed_form_extra_attributes = $this->emailList->allowed_form_extra_attributes;
        $this->honeypot_field = $this->emailList->honeypot_field;
        $this->requires_confirmation = $this->emailList->requires_confirmation;
        $this->redirect_after_subscribed = $this->emailList->redirect_after_subscribed;
        $this->redirect_after_already_subscribed = $this->emailList->redirect_after_already_subscribed;
        $this->redirect_after_subscription_pending = $this->emailList->redirect_after_subscription_pending;
        $this->redirect_after_unsubscribed = $this->emailList->redirect_after_unsubscribed;
        $this->confirmation_mail_id = $this->emailList->confirmation_mail_id;

        if (! $this->emailList->confirmationMail) {
            $this->confirmation_mail_id = null;
        }

        $this->allowed_form_subscription_tags = $this->emailList->allowedFormSubscriptionTags->pluck('name')->toArray();

        $this->confirmation_mail = $this->emailList->hasCustomizedConfirmationMailFields()
            ? self::CONFIRMATION_MAIL_CUSTOM
            : self::CONFIRMATION_MAIL_DEFAULT;
    }

    public function save(): void
    {
        $this->emailList->fill(Arr::except($this->all(), [
            'emailList',
            'allowed_form_subscription_tags',
            'confirmation_mail',
        ]));

        if ($this->confirmation_mail === self::CONFIRMATION_MAIL_DEFAULT) {
            $this->emailList->confirmation_mail_id = null;
        }

        $this->emailList->save();

        $this->emailList->allowedFormSubscriptionTags()->sync(self::getTagClass()::whereIn('name', $this->allowed_form_subscription_tags)->pluck('id'));
    }
}
