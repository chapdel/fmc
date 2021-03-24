<?php

namespace Spatie\Mailcoach\Http\App\Queries;

use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailOpen;
use Spatie\Mailcoach\Http\App\Queries\Filters\FuzzyFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AutomationMailOpensQuery extends QueryBuilder
{
    public int $totalCount;

    public function __construct(AutomationMail $mail)
    {
        $prefix = DB::getTablePrefix();

        $query = AutomationMailOpen::query()
            ->selectRaw("
                {$prefix}mailcoach_automation_mail_opens.subscriber_id as subscriber_id,
                {$prefix}mailcoach_subscribers.email_list_id as subscriber_email_list_id,
                {$prefix}mailcoach_subscribers.email as subscriber_email,
                count({$prefix}mailcoach_automation_mail_opens.subscriber_id) as open_count,
                min({$prefix}mailcoach_automation_mail_opens.created_at) AS first_opened_at
            ")
            ->join('mailcoach_automation_mails', 'mailcoach_automation_mails.id', '=', 'mailcoach_automation_mail_opens.automation_mail_id')
            ->join('mailcoach_subscribers', 'mailcoach_subscribers.id', '=', 'mailcoach_automation_mail_opens.subscriber_id')
            ->where('mailcoach_automation_mails.id', $mail->id);

        $this->totalCount = $query->count();

        parent::__construct($query);

        $this
            ->defaultSort('-first_opened_at')
            ->allowedSorts('email', 'open_count', 'first_opened_at')
            ->groupBy('mailcoach_automation_mail_opens.subscriber_id', 'mailcoach_subscribers.email_list_id', 'mailcoach_subscribers.email')
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
