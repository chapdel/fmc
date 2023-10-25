<?php

namespace Spatie\Mailcoach\Domain\Content\Actions;

use Spatie\Mailcoach\Domain\Content\Models\ContentItem;

class PrepareEmailHtmlAction
{
    public function __construct(
        protected AddUtmTagsToHtmlAction $addUtmTagsToHtmlAction,
        protected CreateDomDocumentFromHtmlAction $createDomDocumentFromHtmlAction,
    ) {
    }

    public function execute(ContentItem $contentItem): void
    {
        $contentItem->email_html = $contentItem->htmlWithInlinedCss();

        if (empty($contentItem->email_html)) {
            $contentItem->save();

            return;
        }

        if ($contentItem->utm_tags) {
            $contentItem->email_html = $this->addUtmTagsToHtmlAction->execute($contentItem->email_html, $contentItem);
        }

        $contentItem->email_html = mb_convert_encoding($contentItem->email_html, 'UTF-8');
        $contentItem->email_html = $this->minifyHtml($contentItem->email_html);

        $contentItem->save();
    }

    protected function minifyHtml(string $html): string
    {
        $replacements = [
            '/(\n|^)(\x20+|\t)/' => "\n",
            '/(\n|^)\/\/(.*?)(\n|$)/' => "\n",
            '/\n/' => ' ',
            '/(\x20+|\t)/' => ' ', // Delete multispace (Without \n)
            '/\>\s+\</' => '><', // strip whitespaces between tags
            '/(\"|\')\s+\>/' => '$1>', // strip whitespaces between quotation ("') and end tags
        ];

        return preg_replace(array_keys($replacements), array_values($replacements), $html);
    }
}
