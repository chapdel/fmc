<?php

namespace Spatie\Mailcoach\Tests\Commands;

use Spatie\Mailcoach\Commands\DeleteOldUnconfirmedSubscribersCommand;
use Spatie\Mailcoach\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class DeleteOldUnconfirmedSubscribersCommandTest extends TestCase
{
    private EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        TestTime::freeze('Y-m-d H:i:s', '2019-01-01 00:00:00');

        $this->emailList = EmailList::factory()->create(['requires_confirmation' => true]);
    }

    /** @test */
    public function it_will_delete_all_unconfirmed_subscribers_that_are_older_than_a_month()
    {
        $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($this->emailList);
        $this->assertEquals(SubscriptionStatus::UNCONFIRMED, $subscriber->status);

        TestTime::addMonth();
        $this->artisan(DeleteOldUnconfirmedSubscribersCommand::class)->assertExitCode(0);
        $this->assertCount(1, Subscriber::all());

        TestTime::addSecond();
        $this->artisan(DeleteOldUnconfirmedSubscribersCommand::class)->assertExitCode(0);
        $this->assertCount(0, Subscriber::all());
    }

    /** @test */
    public function it_will_not_delete_confirmed_subscribers()
    {
        $subscriber = Subscriber::createWithEmail('john@example.com')->skipConfirmation()->subscribeTo($this->emailList);
        $this->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->status);

        TestTime::addMonth()->addSecond();
        $this->artisan(DeleteOldUnconfirmedSubscribersCommand::class)->assertExitCode(0);
        $this->assertCount(1, Subscriber::all());
    }
}
