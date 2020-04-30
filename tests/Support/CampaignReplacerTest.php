<?php

namespace Spatie\Mailcoach\Tests\Support;

use Spatie\Mailcoach\Actions\Campaigns\PrepareEmailHtmlAction;
use Spatie\Mailcoach\Actions\Campaigns\PrepareSubjectAction;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CampaignReplacerTest extends TestCase
{
    use MatchesSnapshots;

    /** @test */
    public function campaignname_should_replaced_in_subject()
    {
        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'subject' => '::campaign.name::',
        ]);

        app(PrepareSubjectAction::class)->execute($campaign);
        $campaign->refresh();
        $replacedhtml = $campaign->subject;
        $this->assertEquals($replacedhtml, $campaign->name);
    }

    /** @test */
    public function campaignname_should_replaced_in_email_html()
    {
        $campaignName = 'test1234';

        /** @var \Spatie\Mailcoach\Models\Campaign */
        $campaign = factory(Campaign::class)->create([
            'name' => $campaignName,
            'html' => '::campaign.name::',
        ]);

        $myHtml = '<html><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd" ><body><p>' . $campaign->name . '</p></body></html>';

        app(PrepareEmailHtmlAction::class)->execute($campaign);
        $campaign->refresh();
        $this->assertMatchesHtmlSnapshotWithoutWhitespace($myHtml);
    }
}
