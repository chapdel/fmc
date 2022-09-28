<?php

use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

it('adds a tag when a automation mail is opened', function () {
    /** @var Send $send */
    $send = SendFactory::new()->create(['campaign_id' => null]);

    $send->automationMail->update(['track_opens' => true]);

    $send->registerOpen();

    expect($send->subscriber->hasTag("automation-mail-{$send->automationMail->id}-opened"))->toBeTrue();

    tap(Tag::first(), function (Tag $tag) {
        expect($tag->type)->toEqual(TagType::MAILCOACH);
    });
});
