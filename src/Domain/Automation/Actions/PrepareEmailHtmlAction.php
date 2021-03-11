<?php

namespace Spatie\Mailcoach\Domain\Automation\Actions;

use DOMDocument;
use Exception;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Automation\Exceptions\CouldNotSendAutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Replacers\AutomationMailReplacer;

class PrepareEmailHtmlAction
{
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
        $dom = new DOMDocument('1.0', 'UTF-8');

        try {
            $html = preg_replace('/&(?!amp;)/', '&amp;', $automationMail->html);

            $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING);

            return true;
        } catch (Exception $exception) {
            throw CouldNotSendAutomationMail::invalidContent($automationMail, $exception);
        }
    }

    protected function replacePlaceholders(AutomationMail $automationMail): void
    {
        $automationMail->email_html = collect(config('mailcoach.campaigns.replacers'))
            ->map(fn (string $className) => resolve($className))
            ->filter(fn (object $class) => $class instanceof AutomationMailReplacer)
            ->reduce(fn (string $html, AutomationMailReplacer $replacer) => $replacer->replace($html, $automationMail), $automationMail->email_html);
    }

    private function addUtmTags(AutomationMail $automationMail): void
    {
        $campaignName = urlencode($automationMail->name);
        $utmTags = "utm_source=newsletter&utm_medium=email&utm_campaign={$campaignName}";

        $automationMail->email_html = $automationMail->htmlLinks()
            ->reduce(function (string $html, string $link) use ($utmTags) {
                $newLink = $link . (parse_url($link, PHP_URL_QUERY) ? '&' : '?') . $utmTags;

                return str_replace($link, $newLink . '', $html);
            }, $automationMail->email_html);
    }
}
