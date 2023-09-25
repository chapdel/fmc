<?php

namespace Spatie\Mailcoach\Domain\Content\Actions;

class AddUtmTagsToHtmlAction
{
    public function __construct(
        protected CreateDomDocumentFromHtmlAction $createDomDocumentFromHtmlAction,
        protected AddUtmTagsToUrlAction $addUtmTagsToUrlAction,
    ) {
    }

    public function execute(string $html, string $name): string
    {
        $document = $this->createDomDocumentFromHtmlAction->execute($html);

        foreach ($document->getElementsByTagName('a') as $linkElement) {
            $url = $linkElement->getAttribute('href');
            $newUrl = $this->addUtmTagsToUrlAction->execute($url, $name);
            $linkElement->setAttribute('href', $newUrl);
        }

        return $document->saveHTML();
    }
}
