<?php

namespace Spatie\Mailcoach\Domain\Shared\Actions;

use Spatie\Mailcoach\Domain\Campaign\Actions\AddUtmTagsToHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Models\Sendable;

class PrepareWebviewHtmlAction
{
    public function __construct(
        protected ReplacePlaceholdersAction $replacePlaceholdersAction,
        protected AddUtmTagsToHtmlAction $addUtmTagsToHtmlAction,
    ) {
    }

    public function execute(Sendable $sendable): void
    {
        $sendable->webview_html = $sendable->htmlWithInlinedCss();
        $sendable->webview_html = $this->replacePlaceholdersAction->execute($sendable->webview_html, $sendable);

        if ($sendable->utm_tags) {
            $sendable->webview_html = $this->addUtmTagsToHtmlAction->execute($sendable->webview_html, $sendable->name);
        }

        $webviewHtml = mb_convert_encoding($sendable->webview_html, 'UTF-8');

        $webviewHtml = $this->removeHiddenTextFromWebview('<!-- webview:hide -->', '<!-- /webview:hide -->', $webviewHtml);
        $webviewHtml = $this->removeHiddenTextFromWebview('<!--webview:hide-->', '<!--/webview:hide-->', $webviewHtml);

        $sendable->webview_html = $webviewHtml;
        $sendable->save();
    }

    protected function removeHiddenTextFromWebview(string $beginTag, string $endTag, string $html): string
    {
        if (! str_contains($html, $beginTag)) {
            return $html;
        }

        $parts = explode($beginTag, $html);

        $matches = [];

        foreach($parts as $part) {
            $matches[] = trim(explode($endTag, $part)[0]);
        }

        $matches = array_filter($matches);
        array_shift($matches);

        // Remove all hide comments
        return str_replace(array_merge([$beginTag, $endTag], $matches), '', $html);
    }
}
