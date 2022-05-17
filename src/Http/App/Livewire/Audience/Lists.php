<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Illuminate\Http\Request;
use Spatie\Mailcoach\Http\App\Livewire\DataTable;
use Spatie\Mailcoach\Http\App\Queries\EmailListQuery;

class Lists extends DataTable
{
    public function deleteList(int $id)
    {
        $list = self::getEmailListClass()::find($id);

        $this->authorize('delete', $list);

        $list->delete();

        $this->flash(__('mailcoach - List :list was deleted.', ['list' => $list->name]));
    }

    public function getTitle(): string
    {
        return __('mailcoach - Lists');
    }

    public function getView(): string
    {
        return 'mailcoach::app.emailLists.index';
    }

    public function getData(Request $request): array
    {
        $this->authorize('viewAny', static::getEmailListClass());

        return [
            'emailLists' => (new EmailListQuery($request))->paginate(),
            'totalEmailListsCount' => static::getEmailListClass()::count(),
        ];
    }
}
