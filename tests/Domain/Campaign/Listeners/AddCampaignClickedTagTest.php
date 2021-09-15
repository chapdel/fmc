<?php

use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink;
use Spatie\Mailcoach\Domain\Shared\Support\LinkHasher;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('adds a tag when a campaign link is clicked', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update(['track_clicks' => true]);

    $send->registerClick('https://spatie.be');

    test()->assertCount(1, CampaignLink::get());

    test()->assertTrue($send->subscriber->hasTag("campaign-{$send->campaign->id}-clicked"));

    $hash = LinkHasher::hash(
        $send->campaign,
        'https://spatie.be',
        'clicked',
    );
    test()->assertTrue($send->subscriber->hasTag($hash));

    tap(Tag::first(), function (Tag $tag) {
        test()->assertEquals(TagType::MAILCOACH, $tag->type);
    });
});
