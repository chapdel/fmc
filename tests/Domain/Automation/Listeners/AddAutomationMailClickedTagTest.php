<?php

use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailLink;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink;
use Spatie\Mailcoach\Domain\Shared\Support\LinkHasher;

it('adds a tag when a automation mail link is clicked', function () {
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create(['campaign_id' => null]);
    $send->automationMail->update(['track_clicks' => true]);

    $send->registerClick('https://spatie.be');

    expect(AutomationMailLink::all())->toHaveCount(1);

    expect($send->subscriber->hasTag("automation-mail-{$send->automationMail->id}-clicked"))->toBeTrue();

    $hash = LinkHasher::hash(
        $send->automationMail,
        'https://spatie.be',
        'clicked',
    );
    expect($send->subscriber->hasTag($hash))->toBeTrue();

    tap(Tag::first(), function (Tag $tag) {
        expect($tag->type)->toEqual(TagType::MAILCOACH);
    });
});
