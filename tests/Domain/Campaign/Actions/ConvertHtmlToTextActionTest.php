<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Actions;

use Spatie\Mailcoach\Domain\Campaign\Actions\ConvertHtmlToTextAction;
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
