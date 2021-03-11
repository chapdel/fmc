<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Exception;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotSendCampaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\CampaignReplacer;
use Spatie\Mailcoach\Domain\Shared\Actions\CreateDomDocumentFromHtmlAction;

class PrepareEmailHtmlAction
{
    public function __construct(
        private CreateDomDocumentFromHtmlAction $createDomDocumentFromHtmlAction
    ) {}

    public function execute(Campaign $campaign): void
    {
        $this->ensureValidHtml($campaign);

        $campaign->email_html = $campaign->htmlWithInlinedCss();

        $this->replacePlaceholders($campaign);

        if ($campaign->utm_tags) {
            $this->addUtmTags($campaign);
        }

        $campaign->save();
    }

    protected function ensureValidHtml(Campaign $campaign)
    {
        try {
            $this->createDomDocumentFromHtmlAction->execute($campaign->html, false);

            return true;
        } catch (Exception $exception) {
            throw CouldNotSendCampaign::invalidContent($campaign, $exception);
        }
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
