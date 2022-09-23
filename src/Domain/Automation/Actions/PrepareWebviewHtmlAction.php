<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Replacers\AutomationMailReplacer;
use Spatie\Mailcoach\Domain\Shared\Actions\AddUtmTagsToUrlAction;
use Spatie\Mailcoach\Domain\Shared\Actions\CreateDomDocumentFromHtmlAction;

class PrepareWebviewHtmlAction
{
    public function __construct(
        private CreateDomDocumentFromHtmlAction $createDomDocumentFromHtmlAction,
        private AddUtmTagsToUrlAction $addUtmTagsToUrlAction,
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
        $automationMail->webview_html = collect(config('mailcoach.automation.replacers'))
            ->map(fn (string $className) => resolve($className))
            ->filter(fn (object $class) => $class instanceof AutomationMailReplacer)
            ->reduce(fn (string $html, AutomationMailReplacer $replacer) => $replacer->replace($html, $automationMail), $automationMail->webview_html);
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
