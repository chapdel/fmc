<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Spatie\Mailcoach\Domain\Campaign\Actions\AddUtmTagsToHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;

class PrepareEmailHtmlAction
{
    public function __construct(
        protected AddUtmTagsToHtmlAction $addUtmTagsToHtmlAction,
        protected CreateDomDocumentFromHtmlAction $createDomDocumentFromHtmlAction,
    ) {
    }

    public function execute(Sendable $sendable): void
    {
        $sendable->email_html = $sendable->htmlWithInlinedCss();

        if (empty($sendable->email_html)) {
            $sendable->save();

            return;
        }

        if ($sendable->utm_tags) {
            $sendable->email_html = $this->addUtmTagsToHtmlAction->execute($sendable->email_html, $sendable->name);
        }

        $sendable->email_html = mb_convert_encoding($sendable->email_html, 'UTF-8');
        $sendable->email_html = $this->minifyHtml($sendable->email_html);

        $sendable->save();
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
