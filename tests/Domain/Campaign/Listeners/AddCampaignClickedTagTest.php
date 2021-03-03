<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Listeners;

use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Domain\Campaign\Models\CampaignLink;
use Spatie\Mailcoach\Domain\Shared\Support\LinkHasher;
use Spatie\Mailcoach\Tests\TestCase;

class AddCampaignClickedTagTest extends TestCase
{
    /** @test * */
    public function it_adds_a_tag_when_a_campaign_link_is_clicked()
    {
        /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
        $send = SendFactory::new()->create();
        $send->campaign->update(['track_clicks' => true]);

        $send->registerClick('https://spatie.be');

        $this->assertCount(1, CampaignLink::get());

        $this->assertTrue($send->subscriber->hasTag("campaign-{$send->campaign->id}-clicked"));

        $hash = LinkHasher::hash(
            $send->campaign,
            'https://spatie.be',
            'clicked',
        );
        $this->assertTrue($send->subscriber->hasTag("campaign-{$send->campaign->id}-clicked-{$hash}"));

        tap(Tag::first(), function (Tag $tag) {
            $this->assertEquals(TagType::MAILCOACH, $tag->type);
        });
    }
}
