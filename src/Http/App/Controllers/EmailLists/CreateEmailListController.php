<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

use Spatie\Mailcoach\Actions\EmailLists\UpdateEmailListAction;
use Spatie\Mailcoach\Http\App\Requests\UpdateEmailListSettingsRequest;
use Spatie\Mailcoach\Traits\UsesMailcoachModels;

class CreateEmailListController
{
    use UsesMailcoachModels;

    public function __invoke(UpdateEmailListSettingsRequest $request, UpdateEmailListAction $updateEmailListAction)
    {
        $emailListClass = $this->getEmailListClass();

        $emailList = new $emailListClass;

        $updateEmailListAction->execute($emailList, $request);

        flash()->success(__('List :emailList was created', ['emailList' => $emailList->name]));

        return redirect()->route('mailcoach.emailLists.subscribers', $emailList);
    }
}
