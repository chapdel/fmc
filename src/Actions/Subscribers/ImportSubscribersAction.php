<?php

namespace Spatie\Mailcoach\Actions\Subscribers;

use Exception;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Mails\ImportSubscribersResultMail;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\SubscriberImport;
use Spatie\Mailcoach\Support\ImportSubscriberRow;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;
use Spatie\SimpleExcel\SimpleExcelReader;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class ImportSubscribersAction
{
    use UsesMailcoachModels;

    public function execute(SubscriberImport $subscriberImport, ?User $user = null)
    {
        $dateTime = $subscriberImport->created_at->format('Y-m-d H:i:s');

        $temporaryDirectory = new TemporaryDirectory(storage_path('temp'));

        $succeededImportsReport = SimpleExcelWriter::create($temporaryDirectory->path("imported-{$dateTime}.csv"))
            ->noHeaderRow();

        $errorReport = SimpleExcelWriter::create($temporaryDirectory->path("errors-{$dateTime}.csv"))
            ->noHeaderRow()
            ->addRow(['Error', 'Values']);

        $localImportFile = $temporaryDirectory->path("import-file-{$dateTime}.csv");

        file_put_contents($localImportFile, stream_get_contents(
            $subscriberImport->getFirstMedia('importFile')->stream()
        ));

        try {
            $this->importSubscribers(
                $localImportFile,
                $subscriberImport->emailList,
                $succeededImportsReport,
                $errorReport
            );
        } catch (Exception $exception) {
            $errorReport->addRow([__("Couldn't finish importing subscribers. This error occurred: :error", ['error' => $exception->getMessage()])]);
        }

        $subscriberImport->update([
            'imported_subscribers_count' => $succeededImportsReport->getNumberOfRows(),
            'error_count' => $errorReport->getNumberOfRows() - 1,
            'status' => SubscriberImportStatus::COMPLETED,
        ]);

        $subscriberImport
            ->addMedia($succeededImportsReport->getPath())
            ->toMediaCollection('importedUsersReport');

        $subscriberImport
            ->addMedia($errorReport->getPath())
            ->toMediaCollection('errorReport');

        $temporaryDirectory->delete();

        if ($user) {
            Mail::mailer(config('mailcoach.mailer') ?? config('mail.default'))
                ->to($user->email)->send(new ImportSubscribersResultMail($subscriberImport));
        }
    }

    protected function importSubscribers(
        string $importFile,
        EmailList $emailList,
        SimpleExcelWriter $succeededImportsReport,
        SimpleExcelWriter $errorReport
    ): void {
        SimpleExcelReader::create($importFile)
            ->getRows()
            ->map(function (array $values) use ($emailList) {
                return new ImportSubscriberRow($emailList, $values);
            })
            ->filter(function (ImportSubscriberRow $row) use ($errorReport) {
                if (! $row->hasValidEmail()) {
                    $this->writeError($errorReport, $row, __('Does not have a valid email'));
                }

                return $row->hasValidEmail();
            })
            ->filter(function (ImportSubscriberRow $row) use ($errorReport) {
                $hasUnsubscribed = $row->hasUnsubscribed();

                if ($hasUnsubscribed) {
                    $this->writeError($errorReport, $row, __('This email address was unsubscribed in the past.'));
                }

                return ! $hasUnsubscribed;
            })
            ->each(function (ImportSubscriberRow $row) use ($emailList, $succeededImportsReport) {
                $attributes = array_merge($row->getAttributes(), ['extra_attributes' => $row->getExtraAttributes()]);

                $subscriber = $this->getSubscriberClass()::createWithEmail($row->getEmail(), $attributes)
                    ->skipConfirmation()
                    ->doNotSendWelcomeMail()
                    ->subscribeTo($emailList);

                if (! is_null($row->tags())) {
                    $subscriber->syncTags($row->tags());
                }

                $succeededImportsReport->addRow($row->getAllValues());
            });
    }

    protected function writeError(SimpleExcelWriter $errorExcel, ImportSubscriberRow $row, string $reason)
    {
        $errorExcel->addRow(array_merge([$reason], $row->getAllValues()));
    }
}
