<?php

namespace Spatie\Mailcoach\Domain\Audience\Actions\Subscribers;

use Exception;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberExportStatus;
use Spatie\Mailcoach\Domain\Audience\Mails\ExportSubscribersResultMail;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberExport;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Livewire\Audience\SubscribersComponent;
use Spatie\Mailcoach\Mailcoach;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Throwable;

class ExportSubscribersAction
{
    use UsesMailcoachModels;

    protected ?User $user;

    protected bool $sendNotification = true;

    protected SubscriberExport $subscriberExport;

    public function execute(
        SubscriberExport $subscriberExport,
        ?User $user = null,
        bool $sendNotification = true
    ): void {
        $this
            ->initialize($subscriberExport, $user, $sendNotification)
            ->exportSubscribers()
            ->notify();
    }

    protected function initialize(SubscriberExport $subscriberExport, ?User $user, bool $sendNotification = true): self
    {
        $this->subscriberExport = $subscriberExport;
        $this->user = $user;
        $this->sendNotification = $sendNotification;

        return $this;
    }

    protected function exportSubscribers(): self
    {
        Auth::login($this->user);

        try {
            $this->subscriberExport->update([
                'status' => SubscriberExportStatus::Exporting,
                'exported_subscribers_count' => 0,
            ]);

            $temporaryDirectory = new TemporaryDirectory(storage_path('temp'));

            $localExportFile = $temporaryDirectory
                ->path("export-file-{$this->subscriberExport->created_at->format('Y-m-d H:i:s')}.csv");

            $writer = SimpleExcelWriter::create($localExportFile);

            $header = [
                'email',
                'first_name',
                'last_name',
                'tags',
                'subscribed_at',
                'unsubscribed_at',
                'extra_attributes',
            ];

            $writer->addHeader($header);

            /** We set up the component to get the same query as the datatable displayed */
            $component = new SubscribersComponent();
            $component->tableFilters = $this->subscriberExport->filters;
            $component->mount($this->subscriberExport->emailList);
            $component->bootedInteractsWithTable();
            $query = $component->getTableQuery();
            $component->filterTableQuery($query);

            $query->lazyById()
                ->each(function (Subscriber $subscriber) use ($header, $writer) {
                    $subscriberData = $subscriber->toExportRow();
                    $defaultData = Arr::only($subscriberData, Arr::except($header, 'extra_attributes'));
                    $defaultData['extra_attributes'] = json_encode(Arr::except($subscriberData, Arr::except($header, 'extra_attributes')));

                    $writer->addRow($defaultData);

                    $this->subscriberExport->increment('exported_subscribers_count');
                });

            $writer->close();

            config()->set('media-library.max_file_size', 1024 * 1024 * 500); // 500MB
            $this->subscriberExport->addMedia($localExportFile)->toMediaCollection('file');

            $this->subscriberExport->update(['status' => SubscriberExportStatus::Completed]);

            $temporaryDirectory->delete();
        } catch (Exception $exception) {
            report($exception);

            $this->subscriberExport->addError($exception->getMessage());
            $this->subscriberExport->update(['status' => SubscriberExportStatus::Failed]);
        }

        return $this;
    }

    protected function notify(): void
    {
        if (! $this->sendNotification) {
            return;
        }

        if (! $this->user) {
            return;
        }

        try {
            Mail::mailer(Mailcoach::defaultTransactionalMailer())
                ->to($this->user->email)->send(new ExportSubscribersResultMail($this->subscriberExport));
        } catch (Throwable $e) {
            report($e);

            return;
        }
    }
}
