<?php

namespace Spatie\Mailcoach\Domain\Shared\Traits;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;

trait UsesMailcoachModels
{
    public static function getCampaignClass(): string
    {
        return config('mailcoach.models.campaign', Campaign::class);
    }

    public static function getEmailListClass(): string
    {
        return config('mailcoach.models.email_list', EmailList::class);
    }

    public function getSendClass(): string
    {
        return config('mailcoach.models.send', Send::class);
    }

    public static function getAutomationClass(): string
    {
        return config('mailcoach.models.automation', Automation::class);
    }

    public static function getAutomationMailClass(): string
    {
        return config('mailcoach.models.automation_mail', AutomationMail::class);
    }

    public static function getSubscriberClass(): string
    {
        return config('mailcoach.models.subscriber', Subscriber::class);
    }

    public static function getTemplateClass(): string
    {
        return config('mailcoach.models.template', Template::class);
    }

    public function getTransactionalMailClass(): string
    {
        return config('mailcoach.models.transactional_mail', TransactionalMail::class);
    }

    public function getTransactionalMailTemplateClass(): string
    {
        return config('mailcoach.models.transactional_mail_template', TransactionalMailTemplate::class);
    }

    public static function getEmailListTableName(): string
    {
        /** @var \Illuminate\Database\Eloquent\Model $emailList */
        $emailListClass = self::getEmailListClass();

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

    public static function getCampaignTableName(): string
    {
        $campaignClass = self::getCampaignClass();

        /** @var \Illuminate\Database\Eloquent\Model $campaign */
        $campaign = new $campaignClass;

        return $campaign->getTable();
    }

    public static function getAutomationMailTableName(): string
    {
        $automationMailClass = self::getAutomationMailClass();

        /** @var \Illuminate\Database\Eloquent\Model $automationMail */
        $automationMail = new $automationMailClass;

        return $automationMail->getTable();
    }
}
