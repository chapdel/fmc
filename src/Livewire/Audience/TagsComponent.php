<?php

namespace Spatie\Mailcoach\Livewire\Audience;

use Closure;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Livewire\TableComponent;
use Spatie\Mailcoach\MainNavigation;

class TagsComponent extends TableComponent
{
    public EmailList $emailList;

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;

        app(MainNavigation::class)->activeSection()
            ?->add($this->emailList->name, route('mailcoach.emailLists.summary', $this->emailList), function ($section) {
                $section->add(__mc('Tags'), route('mailcoach.emailLists.tags', $this->emailList));
            });
    }

    public function deleteTag(Tag $tag)
    {
        $this->authorize('delete', $tag);

        $tag->subscribers->each(function ($subscriber) use ($tag) {
            event(new TagRemovedEvent($subscriber, $tag));
        });

        $tag->delete();

        notify(__mc('Tag :tag was deleted', ['tag' => $tag->name]));
    }

    public function getTitle(): string
    {
        return __mc('Tags');
    }

    public function getLayout(): string
    {
        return 'mailcoach::app.emailLists.layouts.emailList';
    }

    public function getLayoutData(): array
    {
        return [
            'emailList' => $this->emailList,
            'create' => 'tag',
            'createData' => [
                'emailList' => $this->emailList,
            ],
        ];
    }

    protected function getTableQuery(): Builder
    {
        return $this->emailList->tags()->getQuery();
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'name';
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__mc('Name'))
                ->extraAttributes(['class' => 'link'])
                ->sortable()
                ->searchable(),
            IconColumn::make('visible_in_preferences')
                ->label(__mc('Visible'))
                ->alignCenter()
                ->sortable()
                ->boolean(),
            TextColumn::make('subscriber_count')
                ->label(__mc('Subscribers'))
                ->numeric()
                ->view('mailcoach::app.emailLists.tags.columns.subscriber_count')
                ->sortable(query: function ($query) {
                    $query->addSelect(['subscriber_count' => function ($query) {
                        $query
                            ->selectRaw('count(id)')
                            ->from('mailcoach_email_list_subscriber_tags')
                            ->whereColumn('mailcoach_email_list_subscriber_tags.tag_id', self::getTagTableName().'.id');
                    }])->orderBy('subscriber_count', $this->getTableSortDirection());
                })
                ->alignRight(),
            TextColumn::make('updated_at')
                ->label(__mc('Updated at'))
                ->dateTime(config('mailcoach.date_format'))
                ->sortable()
                ->alignRight(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('delete')
                ->label('')
                ->tooltip(__mc('Delete'))
                ->modalHeading('Delete')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->action(fn (Tag $record) => $this->deleteTag($record))
                ->requiresConfirmation(),
        ];
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return __mc('There are no tags for this list. Everyone is equal!');
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-tag';
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return function (Tag $record) {
            return route('mailcoach.emailLists.tags.edit', [$this->emailList, $record]);
        };
    }
}
