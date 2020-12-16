<?php

namespace Spatie\Mailcoach\Tests\Support;

use Illuminate\Support\Facades\Date;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;

class CanBeScheduledTest extends TestCase
{
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign */
    protected $campaign;

    public function setUp(): void
    {
        parent::setUp();

        Date::setTestNow('2020-08-12 09:17');

        $this->campaign = Campaign::factory()->create();
    }

    /** @test * */
    public function it_can_be_scheduled()
    {
        $this->assertNull($this->campaign->scheduled_at);

        $this->campaign->scheduleToBeSentAt(now());

        $this->assertEquals('2020-08-12 09:17', $this->campaign->scheduled_at->toMailcoachFormat());
    }

    /** @test * */
    public function it_stores_the_date_in_utc()
    {
        config()->set('app.timezone', 'Europe/Brussels');

        $this->assertNull($this->campaign->scheduled_at);

        $this->campaign->scheduleToBeSentAt(now()->setTimezone('Europe/Brussels'));

        $this->assertEquals('2020-08-12 11:17', $this->campaign->scheduled_at->toMailcoachFormat());
        $this->assertEquals('2020-08-12 09:17', $this->campaign->scheduled_at->format('Y-m-d H:i'));
    }

    /** @test * */
    public function it_can_be_marked_as_unscheduled()
    {
        $this->campaign->update(['scheduled_at' => now()]);

        $this->campaign->markAsUnscheduled();

        $this->assertNull($this->campaign->scheduled_at);
    }

    /** @test * */
    public function it_scopes_scheduled_campaigns()
    {
        Campaign::factory()->create(['scheduled_at' => now()]);
        Campaign::factory()->create(['scheduled_at' => null]);

        $this->assertEquals(1, Campaign::scheduled()->count());
    }

    /** @test * */
    public function it_scopes_should_be_sent_campaigns()
    {
        Campaign::factory()->create(['scheduled_at' => now()->subDay()]);
        Campaign::factory()->create(['scheduled_at' => now()->addDay()]);

        $this->assertEquals(1, Campaign::shouldBeSentNow()->count());
    }

    /** @test * */
    public function it_scopes_should_be_sent_campaigns_correctly_when_a_timezone_is_set()
    {
        config()->set('app.timezone', 'America/Chicago');

        Campaign::factory()->create()->scheduleToBeSentAt(now()->setTimezone('America/Chicago'));
        Campaign::factory()->create()->scheduleToBeSentAt(now()->addDay()->setTimezone('America/Chicago'));

        $this->assertEquals(1, Campaign::shouldBeSentNow()->count());
    }
}
