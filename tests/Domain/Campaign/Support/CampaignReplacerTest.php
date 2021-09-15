<?php

use Spatie\Mailcoach\Domain\Campaign\Actions\PrepareEmailHtmlAction;
use Spatie\Mailcoach\Domain\Campaign\Actions\PrepareSubjectAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

test('campaignname should replaced in subject', function () {
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign */
    $campaign = Campaign::factory()->create([
        'subject' => '::campaign.name::',
    ]);

    app(PrepareSubjectAction::class)->execute($campaign);
    $campaign->refresh();
    $replacedhtml = $campaign->subject;
    expect($campaign->name)->toEqual($replacedhtml);
});

test('campaignname should replaced in email html', function () {
    $campaignName = 'test1234';

    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign */
    $campaign = Campaign::factory()->create([
        'name' => $campaignName,
        'html' => '::campaign.name::',
    ]);

    $myHtml = '<html><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd" ><body><p>' . $campaign->name . '</p></body></html>';

    app(PrepareEmailHtmlAction::class)->execute($campaign);
    $campaign->refresh();
    test()->assertMatchesHtmlSnapshotWithoutWhitespace($myHtml);
});
