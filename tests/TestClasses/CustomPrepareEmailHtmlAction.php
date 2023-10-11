<?php

namespace Spatie\Mailcoach\Tests\TestClasses;

use Spatie\Mailcoach\Domain\Content\Actions\PrepareEmailHtmlAction;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;

class CustomPrepareEmailHtmlAction extends PrepareEmailHtmlAction
{
    public function execute(ContentItem $contentItem): void
    {
        $contentItem->getModel()->emailList->subscribers->first()->update(['email' => 'overridden@example.com']);

        parent::execute($contentItem);
    }
}
