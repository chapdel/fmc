<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Content\Actions\PrepareEmailHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;

class CustomPrepareEmailHtmlAction extends PrepareEmailHtmlAction
{
    public function execute(Sendable $sendable): void
    {
        $sendable->emailList->subscribers->first()->update(['email' => 'overridden@example.com']);

        parent::execute($sendable);
    }
}
