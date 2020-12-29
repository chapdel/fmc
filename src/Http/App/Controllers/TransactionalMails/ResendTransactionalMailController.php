<?php

namespace Spatie\Mailcoach\Http\App\Controllers\TransactionalMails;

use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

class ResendTransactionalMailController
{
    public function __invoke(TransactionalMail $transactionalMail)
    {
        $transactionalMail->resend();

        flash()->success('The mail has been resent!');

        return back();
    }
}
