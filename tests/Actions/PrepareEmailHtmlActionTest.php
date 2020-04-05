<?php

namespace Spatie\Mailcoach\Tests\Actions;

use Spatie\Mailcoach\Actions\Campaigns\PrepareEmailHtmlAction;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class PrepareEmailHtmlActionTest extends TestCase
{
    use MatchesSnapshots;

    /** @test */
    public function it_will_automatically_add_html_tags()
    {
        $myHtml = '<h1>Hello</h1><p>Hello world</p>';

        $campaign = factory(Campaign::class)->create([
            'track_clicks' => true,
            'html' => $myHtml,
        ]);

        app(PrepareEmailHtmlAction::class)->execute($campaign);

        $campaign->refresh();

        $this->assertMatchesHtmlSnapshotWithoutWhitespace($campaign->email_html);
    }

    /** @test */
    public function it_works_with_ampersands()
    {
        $myHtml = '<html><a href="https://google.com?foo=true&bar=false">Test</a></html>';

        $campaign = factory(Campaign::class)->create([
            'track_clicks' => true,
            'html' => $myHtml,
        ]);

        app(PrepareEmailHtmlAction::class)->execute($campaign);

        $campaign->refresh();

        $this->assertMatchesHtmlSnapshot($campaign->email_html);
    }

    /** @test */
    public function it_will_add_html_tags_if_the_are_already_present()
    {
        $myHtml = '<html><h1>Hello</h1><p>Hello world</p></html>';

        $campaign = factory(Campaign::class)->create([
            'track_clicks' => true,
            'html' => $myHtml,
        ]);

        app(PrepareEmailHtmlAction::class)->execute($campaign);

        $campaign->refresh();

        $this->assertMatchesHtmlSnapshotWithoutWhitespace($campaign->email_html);
    }
}
