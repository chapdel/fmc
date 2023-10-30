<?php

use Spatie\Mailcoach\Domain\Audience\Enums\TagType;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

it('adds no tag when a automationMail is opened and setting is false', function () {
    /** @var Send $send */
    $send = Send::factory()->automationMail()->create();
    $send->contentItem->update([
        'add_subscriber_tags' => false,
    ]);

    $send->registerOpen();

    expect($send->subscriber->hasTag("automation-mail-{$send->contentItem->model->uuid}-opened"))->toBeFalse();
});

it('adds a tag when a automationMail is opened', function () {
    /** @var Send $send */
    $send = Send::factory()->automationMail()->create();
    $send->contentItem->update([
        'add_subscriber_tags' => true,
    ]);

    $send->registerOpen();

    expect($send->subscriber->hasTag("automation-mail-{$send->contentItem->model->uuid}-opened"))->toBeTrue();

    tap(Tag::first(), function (Tag $tag) {
        expect($tag->type)->toEqual(TagType::Mailcoach);
    });
});
