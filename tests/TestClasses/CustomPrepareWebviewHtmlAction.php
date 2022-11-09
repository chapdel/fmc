<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Campaign\Actions\PrepareWebviewHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;

class CustomPrepareWebviewHtmlAction extends PrepareWebviewHtmlAction
{
    public function execute(Sendable $sendable): void
    {
        $sendable->emailList->subscribers->first()->update(['email' => 'overridden@example.com']);

        parent::execute($sendable);
    }
}
