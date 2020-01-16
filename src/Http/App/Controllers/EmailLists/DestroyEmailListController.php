<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

use Spatie\Mailcoach\Models\EmailList;

class DestroyEmailListController
{
    public function __invoke(EmailList $emailList)
    {
        $emailList->delete();

        flash()->success("List {$emailList->name} was deleted.");

        return redirect()->route('mailcoach.emailLists');
    }
}
