<?php

use Spatie\Mailcoach\Domain\Shared\Actions\RenderMarkdownToHtmlAction;

it('can render markdown to html', function () {
    $action = app(RenderMarkdownToHtmlAction::class);

    expect($action->execute(<<<'markdown'
    # Hi there!

    This is some **markdown**
    markdown)->toHtml())->toMatchHtmlSnapshot();
});

it('supports tables', function () {
    $action = app(RenderMarkdownToHtmlAction::class);

    expect($action->execute(<<<'markdown'
    | Syntax      | Description |
    | ----------- | ----------- |
    | Header      | Title       |
    | Paragraph   | Text        |
    markdown)->toHtml())->toMatchHtmlSnapshot();
});
