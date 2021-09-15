<?php

use Spatie\Mailcoach\Domain\Campaign\Actions\ConvertHtmlToTextAction;
use Spatie\Snapshots\MatchesSnapshots;

uses(MatchesSnapshots::class);

it('can convert html to text', function () {
    $html = file_get_contents(__DIR__ . '/stubs/newsletterHtml.txt');

    $text = (new ConvertHtmlToTextAction())->execute($html);

    test()->assertMatchesHtmlSnapshotWithoutWhitespace($text);
});
