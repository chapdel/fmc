<?php

namespace Spatie\Mailcoach\Rules;

use Illuminate\Contracts\Validation\Rule;
use Spatie\Mailcoach\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Models\EmailList;

class EmailListSubscriptionRule implements Rule
{
    /** @var \Spatie\Mailcoach\Models\EmailList */
    protected EmailList $emailList;

    /** @var string */
    protected string $attribute;

    public function __construct(EmailList $emailList)
    {
        $this->emailList = $emailList;
    }

    public function passes($attribute, $value)
    {
        $this->attribute = $attribute;

        return $this->emailList->getSubscriptionStatus($value) !== SubscriptionStatus::SUBSCRIBED;
    }

    public function message()
    {
        return __('This email address is already subscribed.');
    }
}
