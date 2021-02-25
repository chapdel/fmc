<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use DOMDocument;
use Exception;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotSendCampaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\CampaignReplacer;

class PrepareEmailHtmlAction
{
    public function execute(Campaign $campaign): void
    {
        $this->ensureValidHtml($campaign);

        $this->ensureEmailHtmlHasSingleRootElement($campaign);

        $campaign->email_html = $campaign->htmlWithInlinedCss();

        $this->replacePlaceholders($campaign);

        if ($campaign->utm_tags) {
            $this->addUtmTags($campaign);
        }

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

    protected function ensureEmailHtmlHasSingleRootElement($campaign): void
    {
        $docTypeRegex = '~<(?:!DOCTYPE|/?(?:html))[^>]*>\s*~i';

        preg_match($docTypeRegex, $campaign->html, $matches);
        $originalDoctype = $matches[0] ?? null;

        $campaign->html = trim(
            preg_replace($docTypeRegex, '', $campaign->html)
        );

        if (! Str::startsWith(trim($campaign->html), '<html') && $originalDoctype !== '<html>') {
            $campaign->html = '<html>'.$campaign->html;
        }

        if (! Str::endsWith(trim($campaign->html), '</html>')) {
            $campaign->html = $campaign->html.'</html>';
        }

        $campaign->html = $originalDoctype.$campaign->html;
    }

    protected function replacePlaceholders(Campaign $campaign): void
    {
        $campaign->email_html = collect(config('mailcoach.campaigns.replacers'))
            ->map(fn (string $className) => resolve($className))
            ->filter(fn (object $class) => $class instanceof CampaignReplacer)
            ->reduce(fn (string $html, CampaignReplacer $replacer) => $replacer->replace($html, $campaign), $campaign->email_html);
    }

    private function addUtmTags(Campaign $campaign): void
    {
        $campaignName = urlencode($campaign->name);
        $utmTags = "utm_source=newsletter&utm_medium=email&utm_campaign={$campaignName}";

        $campaign->email_html = $campaign->htmlLinks()
            ->reduce(function (string $html, string $link) use ($utmTags) {
                $newLink = $link . (parse_url($link, PHP_URL_QUERY) ? '&' : '?') . $utmTags;

                return str_replace($link, $newLink . '', $html);
            }, $campaign->email_html);
    }
}
