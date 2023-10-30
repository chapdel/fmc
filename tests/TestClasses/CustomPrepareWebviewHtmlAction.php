<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Content\Actions\PrepareWebviewHtmlAction;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;

class CustomPrepareWebviewHtmlAction extends PrepareWebviewHtmlAction
{
    public function execute(ContentItem $contentItem): void
    {
        $contentItem->getModel()->emailList->subscribers->first()->update(['email' => 'overridden@example.com']);

        parent::execute($contentItem);
    }
}
