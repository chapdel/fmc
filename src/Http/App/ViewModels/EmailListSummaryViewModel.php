<?php

namespace Spatie\Mailcoach\Http\App\ViewModels;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\ViewModels\ViewModel;

class EmailListSummaryViewModel extends ViewModel
{
    use UsesMailcoachModels;

    protected EmailList $emailList;

    public function __construct(EmailList $emailList)
    {
        $this->emailList = $emailList;
    }

    public function emailList(): EmailList
    {
        return $this->emailList;
    }
}
