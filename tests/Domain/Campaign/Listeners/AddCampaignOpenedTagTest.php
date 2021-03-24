<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Listeners;

use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;

class AddCampaignOpenedTagTest extends TestCase
{
    /** @test * */
    public function it_adds_a_tag_when_a_campaign_is_opened()
    {
        /** @var Send $send */
        $send = SendFactory::new()->create();

        $send->campaign->update(['track_opens' => true]);

        $send->registerOpen();

        $this->assertTrue($send->subscriber->hasTag("campaign-{$send->campaign->id}-opened"));

        tap(Tag::first(), function (Tag $tag) {
            $this->assertEquals(TagType::MAILCOACH, $tag->type);
        });
    }
}
