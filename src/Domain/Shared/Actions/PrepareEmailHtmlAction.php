<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Spatie\Mailcoach\Domain\Automation\Exceptions\CouldNotSendAutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotSendCampaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;
use Throwable;

class PrepareEmailHtmlAction
{
    public function __construct(
        protected ReplacePlaceholdersAction $replacePlaceholdersAction,
        protected CreateDomDocumentFromHtmlAction $createDomDocumentFromHtmlAction,
        protected AddUtmTagsToUrlAction $addUtmTagsToUrlAction,
    ) {
    }

    public function execute(Sendable $sendable): void
    {
        $this->ensureValidHtml($sendable);

        $sendable->email_html = $sendable->htmlWithInlinedCss();

        $this->replacePlaceholders($sendable);

        if ($sendable->utm_tags) {
            $this->addUtmTags($sendable);
        }

        $sendable->save();
    }

    protected function ensureValidHtml(Sendable $sendable)
    {
        try {
            $this->createDomDocumentFromHtmlAction->execute($sendable->html, false);

            return true;
        } catch (Throwable $exception) {
            if ($sendable instanceof Campaign) {
                throw CouldNotSendCampaign::invalidContent($sendable, $exception);
            }

            if ($sendable instanceof AutomationMail) {
                throw CouldNotSendAutomationMail::invalidContent($sendable, $exception);
            }

            throw $exception;
        }
    }

    protected function replacePlaceholders(Sendable $sendable): void
    {
        $sendable->email_html = $this->replacePlaceholdersAction->execute($sendable->email_html, $sendable);
    }

    protected function addUtmTags(Sendable $sendable): void
    {
        $document = $this->createDomDocumentFromHtmlAction->execute($sendable->email_html);

        foreach ($document->getElementsByTagName('a') as $linkElement) {
            $url = $linkElement->getAttribute('href');
            $newUrl = $this->addUtmTagsToUrlAction->execute($url, $sendable->name);
            $linkElement->setAttribute('href', $newUrl);
        }

        $sendable->email_html = $document->saveHTML();
    }
}
