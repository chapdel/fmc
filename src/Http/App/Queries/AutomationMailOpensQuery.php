<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailOpen;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AutomationMailOpensQuery extends QueryBuilder
{
    use UsesMailcoachModels;

    public int $totalCount;

    public function __construct(AutomationMail $mail)
    {
        $prefix = DB::getTablePrefix();

        $query = AutomationMailOpen::query()
            ->selectRaw("
                {$prefix}mailcoach_automation_mail_opens.subscriber_id as subscriber_id,
                {$prefix}{$this->getSubscriberTableName()}.email_list_id as subscriber_email_list_id,
                {$prefix}{$this->getSubscriberTableName()}.email as subscriber_email,
                count({$prefix}mailcoach_automation_mail_opens.subscriber_id) as open_count,
                min({$prefix}mailcoach_automation_mail_opens.created_at) AS first_opened_at
            ")
            ->join(static::getAutomationMailTableName(), static::getAutomationMailTableName().'.id', '=', 'mailcoach_automation_mail_opens.automation_mail_id')
            ->join($this->getSubscriberTableName(), "{$this->getSubscriberTableName()}.id", '=', 'mailcoach_automation_mail_opens.subscriber_id')
            ->where(static::getAutomationMailTableName().'.id', $mail->id);

        $this->totalCount = $query->count();

        parent::__construct($query);

        $this
            ->defaultSort('-first_opened_at')
            ->allowedSorts('email', 'open_count', 'first_opened_at')
            ->groupBy('mailcoach_automation_mail_opens.subscriber_id', "{$this->getSubscriberTableName()}.email_list_id", "{$this->getSubscriberTableName()}.email")
            ->allowedFilters(
                AllowedFilter::custom(
                    'search',
                    new FuzzyFilter(
                        'email'
                    )
                )
            );
    }
}
