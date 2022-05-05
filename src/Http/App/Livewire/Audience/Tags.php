<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\EmailListTagsQuery;

class Tags extends DataTable
{
    public EmailList $emailList;

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;
    }

    public function deleteTag(int $id)
    {
        $tag = self::getTagClass()::find($id);

        $this->authorize('delete', $tag);

        $tag->subscribers->each(function ($subscriber) use ($tag) {
            event(new TagRemovedEvent($subscriber, $tag));
        });

        $tag->delete();

        $this->flash(__('mailcoach - Tag :tag was deleted', ['tag' => $tag->name]));
    }

    public function getTitle(): string
    {
        return __('mailcoach - Tags');
    }

    public function getView(): string
    {
        return 'mailcoach::app.emailLists.tags.index';
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

    public function getData(): array
    {
        $this->authorize('view', $this->emailList);

        $tagsQuery = new EmailListTagsQuery($this->emailList, request());

        return [
            'emailList' => $this->emailList,
            'tags' => $tagsQuery->paginate(),
            'totalTagsCount' => self::getTagClass()::query()->emailList($this->emailList)->count(),
            'totalDefault' => self::getTagClass()::query()->where('type', TagType::DEFAULT)->emailList($this->emailList)->count(),
            'totalMailcoach' => self::getTagClass()::query()->where('type', TagType::MAILCOACH)->emailList($this->emailList)->count(),
        ];
    }
}
