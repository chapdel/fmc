<?php

namespace Spatie\Mailcoach\Tests\Support;

use Spatie\Mailcoach\Domain\Campaign\Actions\PrepareEmailHtmlAction;
use Spatie\Mailcoach\Domain\Campaign\Actions\PrepareSubjectAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class CampaignReplacerTest extends TestCase
{
    use MatchesSnapshots;

    /** @test */
    public function campaignname_should_replaced_in_subject()
    {
        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign */
        $campaign = Campaign::factory()->create([
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

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign */
        $campaign = Campaign::factory()->create([
            'name' => $campaignName,
            'html' => '::campaign.name::',
        ]);

        $myHtml = '<html><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd" ><body><p>' . $campaign->name . '</p></body></html>';

        app(PrepareEmailHtmlAction::class)->execute($campaign);
        $campaign->refresh();
        $this->assertMatchesHtmlSnapshotWithoutWhitespace($myHtml);
    }
}
