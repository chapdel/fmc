<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Spatie\Mailcoach\Domain\Campaign\Actions\AddUtmTagsToHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;

class PrepareEmailHtmlAction
{
    public function __construct(
        protected AddUtmTagsToHtmlAction $addUtmTagsToHtmlAction,
        protected CreateDomDocumentFromHtmlAction $createDomDocumentFromHtmlAction,
    ) {
    }

    public function execute(Sendable $sendable): void
    {
        $sendable->email_html = $sendable->htmlWithInlinedCss();

        if ($sendable->utm_tags) {
            $sendable->email_html = $this->addUtmTagsToHtmlAction->execute($sendable->email_html, $sendable->name);
        }

        $sendable->email_html = mb_convert_encoding($sendable->email_html, 'UTF-8');

        $sendable->save();
    }
}
