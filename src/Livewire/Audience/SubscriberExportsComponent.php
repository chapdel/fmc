<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberExportStatus;
use Spatie\Mailcoach\Domain\Audience\Jobs\ExportSubscribersJob;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberExport;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\MainNavigation;

class SubscriberExportsComponent extends TableComponent
{
    public EmailList $emailList;

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;

        app(MainNavigation::class)->activeSection()?->add($this->emailList->name, route('mailcoach.emailLists'));
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    public function downloadFile(SubscriberExport $subscriberExport)
    {
        abort_unless((bool) $subscriberExport->getMediaCollection('file'), 403);

        return $subscriberExport->getFirstMedia('file');
    }

    public function deleteExport(SubscriberExport $export)
    {
        $this->authorize('delete', $export);

        $export->delete();

        $this->flash(__mc('Export was deleted.'));
    }

    public function restartExport(SubscriberExport $export): void
    {
        $export->update([
            'status' => SubscriberExportStatus::Pending,
            'errors' => [],
        ]);

        dispatch(new ExportSubscribersJob($export, Auth::user()));

        $this->flash(__mc('Export successfully restarted.'));
    }

    public function getTitle(): string
    {
        return __mc('Subscriber exports');
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.emailLists.layouts.emailList';
    }

    public function getLayoutData(): array
    {
        return [
            'emailList' => $this->emailList,
        ];
    }

    protected function getTableQuery(): Builder
    {
        return self::getSubscriberExportClass()::query()
            ->where('email_list_id', $this->emailList->id)
            ->with('emailList');
    }

    protected function getTableColumns(): array
    {
        return [
            IconColumn::make('status')
                ->icon(fn (SubscriberExport $export) => match (true) {
                    $export->status === SubscriberExportStatus::Exporting => 'heroicon-o-arrow-path',
                    $export->status === SubscriberExportStatus::Pending => 'heroicon-o-clock',
                    $export->status === SubscriberExportStatus::Completed => 'heroicon-o-check-circle',
                    $export->status === SubscriberExportStatus::Failed => 'heroicon-o-exclamation-circle',
                })
                ->tooltip(fn (SubscriberExport $export) => match (true) {
                    $export->status === SubscriberExportStatus::Exporting => __mc('Exporting'),
                    $export->status === SubscriberExportStatus::Pending => __mc('Pending'),
                    $export->status === SubscriberExportStatus::Completed => __mc('Completed'),
                    $export->status === SubscriberExportStatus::Failed => __mc('Failed'),
                })
                ->color(fn (SubscriberExport $export) => match (true) {
                    $export->status === SubscriberExportStatus::Exporting => 'warning',
                    $export->status === SubscriberExportStatus::Pending => 'warning',
                    $export->status === SubscriberExportStatus::Completed => 'success',
                    $export->status === SubscriberExportStatus::Failed => 'danger',
                })
                ->extraAttributes(fn (SubscriberExport $export) => match (true) {
                    $export->status === SubscriberExportStatus::Exporting => ['class' => 'fa-spin'],
                    $export->status === SubscriberExportStatus::Pending => ['class' => 'fa-spin'],
                    default => [],
                })
                ->sortable(),
            TextColumn::make('created_at')
                ->label(__mc('Started at'))
                ->sortable()
                ->dateTime(config('mailcoach.date_format')),
            TextColumn::make('exported_subscribers_count')
                ->sortable()
                ->label(__mc('Exported subscribers'))
                ->numeric(),
            TextColumn::make('errors')
                ->getStateUsing(fn (SubscriberExport $export) => implode('<br/>', $export->errors ?? []))
                ->html()
                ->numeric()
                ->label(__mc('Errors')),
        ];
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return __mc('Create an export from the subscribers overview.');
    }

    protected function getTablePollingInterval(): ?string
    {
        return '5s';
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('download-file')
                    ->label(__mc('Download export'))
                    ->icon('heroicon-o-document-text')
                    ->action(fn (SubscriberExport $export) => $this->downloadFile($export))
                    ->hidden(fn (SubscriberExport $export) => ! $export->hasMedia('file')),
                Action::make('restart')
                    ->label(__mc('Restart'))
                    ->requiresConfirmation()
                    ->icon('heroicon-o-arrow-path')
                    ->action(fn (SubscriberExport $export) => $this->restartExport($export)),
                Action::make('delete')
                    ->label(__mc('Delete'))
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (SubscriberExport $export) => $this->deleteExport($export))
                    ->authorize('delete', self::getSubscriberExportClass()),
            ]),
        ];
    }
}
