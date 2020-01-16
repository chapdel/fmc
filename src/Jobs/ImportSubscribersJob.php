<?php

namespace Spatie\Mailcoach\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Auth\User;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Actions\Campaigns\CalculateStatisticsAction;
use Spatie\Mailcoach\Actions\Subscribers\ImportSubscribersAction;
use Spatie\Mailcoach\Mails\ImportSubscribersResultMail;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Models\SubscriberImport;
use Spatie\Mailcoach\Support\Config;
use Spatie\Mailcoach\Support\ImportSubscriberRow;
use Spatie\SimpleExcel\SimpleExcelReader;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class ImportSubscribersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public SubscriberImport $subscriberImport;

    public ?User $user;

    public function __construct(SubscriberImport $subscriberImport, User $user = null)
    {
        $this->subscriberImport = $subscriberImport;

        $this->user = $user;
    }

    public function handle()
    {
        /** @var \Spatie\Mailcoach\Actions\Subscribers\ImportSubscribersAction $importSubscribersAction */
        $importSubscribersAction = Config::getActionClass('import_subscribers', ImportSubscribersAction::class);

        $importSubscribersAction->execute($this->subscriberImport, $this->user);
    }
}
