<?php

namespace Spatie\Mailcoach\Tests\Actions;

use Spatie\Mailcoach\Actions\Campaigns\ConvertHtmlToTextAction;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class ConvertHtmlToTextActionTest extends TestCase
{
    use MatchesSnapshots;

    /** @test */
    public function it_can_convert_html_to_text()
    {
        $html = file_get_contents(__DIR__ . '/stubs/newsletterHtml.txt');

        $text = (new ConvertHtmlToTextAction())->execute($html);

        $this->assertMatchesHtmlSnapshotWithoutWhitespace($text);
    }
}
