<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers;

use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Http\Front\Controllers\EmailListCampaignsFeedController;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Tests\TestCase;

class EmailListCampaignsFeedControllerTest extends TestCase
{
    private EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->emailList = factory(EmailList::class)->create([
            'campaigns_feed_enabled' => true,
        ]);

        factory(Campaign::class)->create([
            'email_list_id' => $this->emailList->id,
            'sent_at' => now(),
            'status' => CampaignStatus::SENT,
        ]);
    }

    /** @test */
    public function it_can_generate_a_feed()
    {
        $this
            ->get(action(EmailListCampaignsFeedController::class, $this->emailList->uuid))
            ->assertSee('<?xml');
    }

    /** @test */
    public function it_will_not_display_a_feed_if_it_is_not_enabled()
    {
        $this->emailList->update(['campaigns_feed_enabled' => false]);

        $this
            ->get(action(EmailListCampaignsFeedController::class, $this->emailList->uuid))
            ->assertStatus(404);
    }
}
