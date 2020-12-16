<?php

namespace Spatie\Mailcoach\Traits;

use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Send;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Models\Template;
use Spatie\Mailcoach\Models\TransactionalMail;

trait UsesMailcoachModels
{
    public function getCampaignClass(): string
    {
        return config('mailcoach.models.campaign', Campaign::class);
    }

    public function getEmailListClass(): string
    {
        return config('mailcoach.models.email_list', EmailList::class);
    }

    public function getSendClass(): string
    {
        return config('mailcoach.models.send', Send::class);
    }

    public function getSubscriberClass(): string
    {
        return config('mailcoach.models.subscriber', Subscriber::class);
    }

    public function getTemplateClass(): string
    {
        return config('mailcoach.models.template', Template::class);
    }

    public function getTransactionalMailClass(): string
    {
        return config('mailcoach.models.transactional_mail', TransactionalMail::class);
    }

    public function getEmailListTableName(): string
    {
        /** @var \Illuminate\Database\Eloquent\Model $emailList */
        $emailListClass = $this->getEmailListClass();

        $emailList = new $emailListClass;

        return $emailList->getTable();
    }

    public function getSubscriberTableName(): string
    {
        /** @var \Illuminate\Database\Eloquent\Model $subscriber */
        $subscriberClass = $this->getSubscriberClass();

        $subscriber = new $subscriberClass;

        return $subscriber->getTable();
    }
}
