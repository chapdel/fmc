<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Features\SupportRedirects\Redirector;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\MainNavigation;

class SegmentsComponent extends TableComponent
{
    public EmailList $emailList;

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;

        app(MainNavigation::class)->activeSection()?->add($this->emailList->name, route('mailcoach.emailLists.segments', $this->emailList));
    }

    protected function getTableQuery(): Builder
    {
        return $this->emailList->segments()->getQuery();
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'name';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return __mc('A segment is a group of tags that can be targeted by an email campaign. You can learn more about segmentation & tags in our docs');
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Action::make(__mc('Learn more'))
                ->url('https://mailcoach.app/docs/self-hosted/v6/using-mailcoach/email-lists/segmentation-tags')
                ->link()
                ->extraAttributes(['class' => 'link'])
                ->openUrlInNewTab(),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__mc('Name'))
                ->sortable()
                ->searchable()
                ->extraAttributes(['class' => 'link']),
            TextColumn::make('population')
                ->name(__mc('Population'))
                ->getStateUsing(fn (TagSegment $segment) => Str::shortNumber($segment->getSubscribersQuery()->count()))
                ->alignRight(),
            TextColumn::make('created_at')
                ->date(config('mailcoach.date_format'))
                ->alignRight()
                ->sortable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('Duplicate')
                    ->action(fn (TagSegment $record) => $this->duplicateSegment($record))
                    ->icon('heroicon-o-clipboard')
                    ->label(__mc('Duplicate')),
                Action::make('Delete')
                    ->action(fn (TagSegment $record) => $record->delete())
                    ->requiresConfirmation()
                    ->label(__mc('Delete'))
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ]),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (TagSegment $segment) {
            return route('mailcoach.emailLists.segments.edit', [$this->emailList, $segment]);
        };
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-user-group';
    }

    public function duplicateSegment(TagSegment $segment): RedirectResponse|Redirector
    {
        $this->authorize('update', $this->emailList);

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\TagSegment $duplicateSegment */
        $duplicateSegment = self::getTagSegmentClass()::create([
            'name' => "{$segment->name} - ".__mc('copy'),
            'email_list_id' => $segment->email_list_id,
        ]);

        flash()->success(__mc('Segment :segment was duplicated.', ['segment' => $segment->name]));

        return redirect()->route('mailcoach.emailLists.segments.edit', [
            $duplicateSegment->emailList,
            $duplicateSegment,
        ]);
    }

    public function getTitle(): string
    {
        return __mc('Segments');
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.emailLists.layouts.emailList';
    }

    public function getLayoutData(): array
    {
        $data = [
            'emailList' => $this->emailList,
        ];

        if (Auth::user()->can('create', self::getTagSegmentClass())) {
            $data['create'] = 'segment';
            $data['createData'] = [
                'emailList' => $this->emailList,
            ];
        }

        return $data;
    }
}
