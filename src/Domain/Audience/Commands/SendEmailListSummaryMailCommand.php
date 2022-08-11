<?php

namespace Spatie\Mailcoach\Domain\Audience\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Jobs\SendEmailListSummaryMailJob;
use Spatie\Mailcoach\Domain\Audience\Mails\EmailListSummaryMail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Mailcoach;

class SendEmailListSummaryMailCommand extends Command
{
    use UsesMailcoachModels;

    protected $signature = 'mailcoach:send-email-list-summary-mail';

    public $description = 'Send a summary mail on the subscribers of a list';

    public function handle()
    {
        dispatch(new SendEmailListSummaryMailJob());
    }
}
