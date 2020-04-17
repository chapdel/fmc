<?php

namespace Spatie\Mailcoach\Actions\Campaigns;

use DOMDocument;
use Exception;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Exceptions\CouldNotSendCampaign;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Support\Replacers\Replacer;

class PrepareEmailHtmlAction
{
    public function execute(Campaign $campaign)
    {
        $this->ensureValidHtml($campaign);

        $campaign->email_html = $campaign->htmlWithInlinedCss();

        $this->ensureEmailHtmlHasSingleRootElement($campaign);

        $this->replacePlaceholders($campaign);

        $campaign->save();
    }

    protected function ensureValidHtml(Campaign $campaign)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');

        try {
            $html = preg_replace('/&(?!amp;)/', '&amp;', $campaign->html);

            $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOWARNING);

            return true;
        } catch (Exception $exception) {
            throw CouldNotSendCampaign::invalidContent($campaign, $exception);
        }
    }

    protected function ensureEmailHtmlHasSingleRootElement($campaign)
    {
        if (! Str::startsWith(trim($campaign->email_html), '<html')) {
            $campaign->email_html = '<html>'.$campaign->email_html;
        }

        if (! Str::endsWith(trim($campaign->email_html), '</html>')) {
            $campaign->email_html = $campaign->email_html.'</html>';
        }
    }

    protected function replacePlaceholders(Campaign $campaign): void
    {
        $campaign->email_html = collect(config('mailcoach.replacers'))
            ->map(fn (string $className) => app($className))
            ->filter(fn (object $class) => $class instanceof Replacer)
            ->reduce(fn (string $html, Replacer $replacer) => $replacer->replace($html, $campaign), $campaign->email_html);
    }
}
