<?php

namespace Spatie\Mailcoach\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\CampaignReplacer;
use Spatie\Mailcoach\Domain\Shared\Actions\AddUtmTagsToUrlAction;
use Spatie\Mailcoach\Domain\Shared\Actions\CreateDomDocumentFromHtmlAction;

class PrepareWebviewHtmlAction
{
    public function __construct(
        protected CreateDomDocumentFromHtmlAction $createDomDocumentFromHtmlAction,
        protected AddUtmTagsToUrlAction $addUtmTagsToUrlAction,
    ) {
    }

    public function execute(Campaign $campaign): void
    {
        $campaign->webview_html = $campaign->htmlWithInlinedCss();

        $this->replacePlaceholders($campaign);

        if ($campaign->utm_tags) {
            $this->addUtmTags($campaign);
        }

        $campaign->save();
    }

    protected function replacePlaceholders(Campaign $campaign): void
    {
        $campaign->webview_html = collect(config('mailcoach.campaigns.replacers'))
            ->map(fn (string $className) => resolve($className))
            ->filter(fn (object $class) => $class instanceof CampaignReplacer)
            ->reduce(fn (string $html, CampaignReplacer $replacer) => $replacer->replace($html, $campaign), $campaign->webview_html);
    }

    private function addUtmTags(Campaign $campaign): void
    {
        $document = $this->createDomDocumentFromHtmlAction->execute($campaign->webview_html);

        foreach ($document->getElementsByTagName('a') as $linkElement) {
            $url = $linkElement->getAttribute('href');
            $newUrl = $this->addUtmTagsToUrlAction->execute($url, $campaign->name);
            $linkElement->setAttribute('href', $newUrl);
        }

        $campaign->webview_html = $document->saveHTML();
    }
}
