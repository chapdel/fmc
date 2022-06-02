<?php

use Spatie\Mailcoach\Domain\Campaign\Actions\PrepareEmailHtmlAction;
use Spatie\Mailcoach\Domain\Campaign\Actions\PrepareSubjectAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

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

    app(PrepareEmailHtmlAction::class)->execute($campaign);
    $campaign->refresh();
    test()->assertMatchesHtmlSnapshotWithoutWhitespace($campaign->email_html);
});

test('campaignname should replace in url encoded html', function () {
    $campaignName = 'test1234';

    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign */
    $campaign = Campaign::factory()->create([
        'name' => $campaignName,
        'html' => urlencode('::campaign.name::'),
    ]);

    app(PrepareEmailHtmlAction::class)->execute($campaign);
    $campaign->refresh();
    test()->assertMatchesHtmlSnapshotWithoutWhitespace($campaign->email_html);
});
