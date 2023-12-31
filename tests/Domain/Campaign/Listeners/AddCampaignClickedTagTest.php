<?php

use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Enums\TagType;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Content\Models\Link;
use Spatie\Mailcoach\Domain\Content\Support\LinkHasher;

it('adds no tag when a campaign link is clicked and setting is off', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->contentItem->update([
        'add_subscriber_tags' => false,
        'add_subscriber_link_tags' => false,
    ]);

    $send->registerClick('https://spatie.be');

    expect(Link::get())->toHaveCount(1);
    expect($send->subscriber->tags->count())->toBe(0);
});

it('adds a tag when a campaign link is clicked', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->contentItem->update([
        'add_subscriber_tags' => true,
        'add_subscriber_link_tags' => false,
    ]);

    $send->registerClick('https://spatie.be');

    expect(Link::get())->toHaveCount(1);

    expect($send->subscriber->hasTag("campaign-{$send->contentItem->model->uuid}-clicked"))->toBeTrue();

    $hash = LinkHasher::hash(
        $send->contentItem->model,
        'https://spatie.be',
    );
    expect($send->subscriber->hasTag($hash))->toBeFalse();

    tap(Tag::first(), function (Tag $tag) {
        expect($tag->type)->toEqual(TagType::Mailcoach);
    });
});

it('adds a tag per link when a campaign link is clicked', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();
    $send->contentItem->update([
        'add_subscriber_tags' => false,
        'add_subscriber_link_tags' => true,
    ]);

    $send->registerClick('https://spatie.be');

    expect(Link::get())->toHaveCount(1);

    expect($send->subscriber->hasTag("campaign-{$send->contentItem->model->uuid}-clicked"))->toBeFalse();

    $hash = LinkHasher::hash(
        $send->contentItem->model,
        'https://spatie.be',
    );
    expect($send->subscriber->hasTag($hash))->toBeTrue();

    tap(Tag::first(), function (Tag $tag) {
        expect($tag->type)->toEqual(TagType::Mailcoach);
    });
});
