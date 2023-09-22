<?php

namespace Spatie\Mailcoach\Domain\Content\Actions;

use Spatie\Mailcoach\Domain\Campaign\Actions\AddUtmTagsToHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Actions\ReplacePlaceholdersAction;
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
        if ($sendable->disable_webview) {
            $sendable->contentItem->webview_html = null;
            $sendable->contentItem->save();

            return;
        }

        $contentItem = $sendable->contentItem;

        $contentItem->webview_html = $contentItem->htmlWithInlinedCss();
        $contentItem->webview_html = $this->replacePlaceholdersAction->execute($contentItem->webview_html, $sendable);

        if (empty(trim($contentItem->webview_html))) {
            $contentItem->save();

            return;
        }

        if ($contentItem->utm_tags) {
            $contentItem->webview_html = $this->addUtmTagsToHtmlAction->execute($contentItem->webview_html, $sendable->name);
        }

        $webviewHtml = mb_convert_encoding($contentItem->webview_html, 'UTF-8');

        $webviewHtml = $this->removeHiddenTextFromWebview('<!-- webview:hide -->', '<!-- /webview:hide -->', $webviewHtml);
        $webviewHtml = $this->removeHiddenTextFromWebview('<!--webview:hide-->', '<!--/webview:hide-->', $webviewHtml);

        $contentItem->webview_html = $webviewHtml;
        $contentItem->save();
    }

    protected function removeHiddenTextFromWebview(string $beginTag, string $endTag, string $html): string
    {
        if (! str_contains($html, $beginTag)) {
            return $html;
        }

        $parts = explode($beginTag, $html);

        $matches = [];

        foreach ($parts as $part) {
            $matches[] = trim(explode($endTag, $part)[0]);
        }

        $matches = array_filter($matches);
        array_shift($matches);

        // Remove all hide comments
        return str_replace([$beginTag, $endTag, ...$matches], '', $html);
    }
}
