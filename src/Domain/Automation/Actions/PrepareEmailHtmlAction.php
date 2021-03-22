<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use Exception;
use Spatie\Mailcoach\Domain\Automation\Exceptions\CouldNotSendAutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Replacers\AutomationMailReplacer;
use Spatie\Mailcoach\Domain\Shared\Actions\AddUtmTagsToUrlAction;
use Spatie\Mailcoach\Domain\Shared\Actions\CreateDomDocumentFromHtmlAction;

class PrepareEmailHtmlAction
{
    public function __construct(
        private CreateDomDocumentFromHtmlAction $createDomDocumentFromHtmlAction,
        private AddUtmTagsToUrlAction $addUtmTagsToUrlAction,
    ) {
    }

    public function execute(AutomationMail $automationMail): void
    {
        $this->ensureValidHtml($automationMail);

        $automationMail->email_html = $automationMail->htmlWithInlinedCss();

        $this->replacePlaceholders($automationMail);

        if ($automationMail->utm_tags) {
            $this->addUtmTags($automationMail);
        }

        $automationMail->save();
    }

    protected function ensureValidHtml(AutomationMail $automationMail)
    {
        try {
            $this->createDomDocumentFromHtmlAction->execute($automationMail->html, false);

            return true;
        } catch (Exception $exception) {
            throw CouldNotSendAutomationMail::invalidContent($automationMail, $exception);
        }
    }

    protected function replacePlaceholders(AutomationMail $automationMail): void
    {
        $automationMail->email_html = collect(config('mailcoach.automation.replacers'))
            ->map(fn (string $className) => resolve($className))
            ->filter(fn (object $class) => $class instanceof AutomationMailReplacer)
            ->reduce(fn (string $html, AutomationMailReplacer $replacer) => $replacer->replace($html, $automationMail), $automationMail->email_html);
    }

    private function addUtmTags(AutomationMail $automationMail): void
    {
        $replacements = $automationMail->htmlLinks()
            ->mapWithKeys(function (string $link) use ($automationMail) {
                $newLink = $this->addUtmTagsToUrlAction->execute($link, $automationMail->name);

                return [$link => $newLink];
            });

        $automationMail->email_html = strtr(
            $automationMail->email_html,
            $replacements->toArray(),
        );
    }
}
