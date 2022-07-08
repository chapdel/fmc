<?php

use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink;
use Spatie\Mailcoach\Domain\Shared\Support\LinkHasher;

it('adds a tag when a campaign link is clicked', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update();

    $send->registerClick('https://spatie.be');

    expect(CampaignLink::get())->toHaveCount(1);

    expect($send->subscriber->hasTag("campaign-{$send->campaign->id}-clicked"))->toBeTrue();

    $hash = LinkHasher::hash(
        $send->campaign,
        'https://spatie.be',
        'clicked',
    );
    expect($send->subscriber->hasTag($hash))->toBeTrue();

    tap(Tag::first(), function (Tag $tag) {
        expect($tag->type)->toEqual(TagType::Mailcoach);
    });
});
