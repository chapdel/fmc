<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Campaign\Actions\EmailLists\UpdateEmailListAction;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Requests\EmailLists\UpdateEmailListSettingsRequest;

class CreateEmailListController
{
    use AuthorizesRequests,
        UsesMailcoachModels;

    public function __invoke(UpdateEmailListSettingsRequest $request, UpdateEmailListAction $updateEmailListAction)
    {
        $this->authorize('create', EmailList::class);

        $emailListClass = $this->getEmailListClass();

        $emailList = new $emailListClass;

        $updateEmailListAction->execute($emailList, $request);

        flash()->success(__('List :emailList was created', ['emailList' => $emailList->name]));

        return redirect()->route('mailcoach.emailLists.settings', $emailList);
    }
}
