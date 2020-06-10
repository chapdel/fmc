<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

use Spatie\Mailcoach\Models\EmailList;

class DestroyEmailListController
{
    public function __invoke(EmailList $emailList)
    {
        $emailList->delete();

        flash()->success(__('List :emailList was deleted', ['emailList' => $emailList->name]));

        return redirect()->route('mailcoach.emailLists');
    }
}
