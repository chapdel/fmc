<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Shared\Actions\AddUtmTagsToUrlAction;
use Spatie\Mailcoach\Domain\Shared\Actions\CreateDomDocumentFromHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Actions\ReplacePlaceholdersAction;

class PrepareWebviewHtmlAction
{
    public function __construct(
        protected ReplacePlaceholdersAction $replacePlaceholdersAction,
        protected CreateDomDocumentFromHtmlAction $createDomDocumentFromHtmlAction,
        protected AddUtmTagsToUrlAction $addUtmTagsToUrlAction,
    ) {
    }

    public function execute(AutomationMail $automationMail): void
    {
        $automationMail->webview_html = $automationMail->htmlWithInlinedCss();

        $this->replacePlaceholders($automationMail);

        if ($automationMail->utm_tags) {
            $this->addUtmTags($automationMail);
        }

        $automationMail->save();
    }

    protected function replacePlaceholders(AutomationMail $automationMail): void
    {
        $automationMail->webview_html = $this->replacePlaceholdersAction->execute($automationMail->webview_html, $automationMail);
    }

    protected function addUtmTags(AutomationMail $automationMail): void
    {
        $document = $this->createDomDocumentFromHtmlAction->execute($automationMail->webview_html);

        foreach ($document->getElementsByTagName('a') as $linkElement) {
            $url = $linkElement->getAttribute('href');
            $newUrl = $this->addUtmTagsToUrlAction->execute($url, $automationMail->name);
            $linkElement->setAttribute('href', $newUrl);
        }

        $automationMail->webview_html = $document->saveHTML();
    }
}
