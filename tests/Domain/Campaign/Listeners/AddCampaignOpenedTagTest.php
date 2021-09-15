<?php

use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;



it('adds a tag when a campaign is opened', function () {
    /** @var Send $send */
    $send = SendFactory::new()->create();

    $send->campaign->update(['track_opens' => true]);

    $send->registerOpen();

    expect($send->subscriber->hasTag("campaign-{$send->campaign->id}-opened"))->toBeTrue();

    tap(Tag::first(), function (Tag $tag) {
        expect($tag->type)->toEqual(TagType::MAILCOACH);
    });
});
