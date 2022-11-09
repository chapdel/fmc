<?php

use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

it('adds no tag when a campaign is opened and setting is false', function () {
    /** @var Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update([
        'add_subscriber_tags' => false,
    ]);

    $send->registerOpen();

    expect($send->subscriber->hasTag("campaign-{$send->campaign->uuid}-opened"))->toBeFalse();
});

it('adds a tag when a campaign is opened', function () {
    /** @var Send $send */
    $send = SendFactory::new()->create();
    $send->campaign->update([
        'add_subscriber_tags' => true,
    ]);

    $send->registerOpen();

    expect($send->subscriber->hasTag("campaign-{$send->campaign->uuid}-opened"))->toBeTrue();

    tap(Tag::first(), function (Tag $tag) {
        expect($tag->type)->toEqual(TagType::Mailcoach);
    });
});
