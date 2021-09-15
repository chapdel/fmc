<?php

use Spatie\Mailcoach\Domain\Automation\Actions\ConvertHtmlToTextAction;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

uses(TestCase::class);
uses(MatchesSnapshots::class);

it('can convert html to text', function () {
    $html = file_get_contents(__DIR__ . '/stubs/newsletterHtml.txt');

    $text = (new ConvertHtmlToTextAction())->execute($html);

    test()->assertMatchesHtmlSnapshotWithoutWhitespace($text);
});
