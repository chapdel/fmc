<?php

namespace Spatie\Mailcoach;

use Illuminate\Support\Collection;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;

class MailcoachRegistrar
{
    /** @var string */
    protected $campaignClass;

    /** @var string */
    protected $emailListClass;

    /** @var string */
    protected $subscriberClass;

    /**
     * MailcoachRegistrar constructor.
     *
     */
    public function __construct()
    {
        $this->campaignClass = config('mailcoach.models.campaign');
        $this->emailListClass = config('mailcoach.models.email_list');
        $this->subscriberClass = config('mailcoach.models.subscriber');
    }

    /**
     * Get an instance of the Campaign class.
     *
     * @return \Spatie\Mailcoach\Model\Campaign
     */
    public function getCampaignClass(): Campaign
    {
        return app($this->campaignClass);
    }

    public function setCampaignClass($campaignClass)
    {
        $this->campaignClass = $campaignClass;

        return $this;
    }

    /**
     * Get an instance of the EmailList class.
     *
     * @return \Spatie\Mailcoach\Model\EmailList
     */
    public function getEmailListClass(): EmailList
    {
        return app($this->emailListClass);
    }

    public function setEmailListClass($emailListClass)
    {
        $this->emailListClass = $emailListClass;

        return $this;
    }

    /**
     * Get an instance of the Subscriber class.
     *
     * @return \Spatie\Mailcoach\Model\Subscriber
     */
    public function getSubscriberClass(): Subscriber
    {
        return app($this->subscriberClass);
    }

    public function setSubscriberClass($subscriberClass)
    {
        $this->subscriberClass = $subscriberClass;

        return $this;
    }
}
