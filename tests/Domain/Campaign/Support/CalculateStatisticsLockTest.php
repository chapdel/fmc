<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Support;

use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Support\CalculateStatisticsLock;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class CalculateStatisticsLockTest extends TestCase
{
    /** @var \Spatie\Mailcoach\Domain\Campaign\Support\CalculateStatisticsLock */
    protected CalculateStatisticsLock $lock;

    public function setUp(): void
    {
        parent::setUp();

        $this->campaign = Campaign::factory()->create();

        $this->lock = new CalculateStatisticsLock($this->campaign);

        TestTime::freeze();
    }

    /** @test */
    public function it_can_lock_and_release()
    {
        $this->assertTrue($this->lock->get());

        $this->assertFalse($this->lock->get());

        $this->lock->release();

        $this->assertTrue($this->lock->get());
    }

    /** @test */
    public function it_will_automatically_expire_the_lock_after_10_seconds()
    {
        $this->assertTrue($this->lock->get());

        $this->assertFalse($this->lock->get());

        TestTime::addSeconds(9);
        $this->assertFalse($this->lock->get());

        TestTime::addSecond();
        $this->assertTrue($this->lock->get());
        $this->assertFalse($this->lock->get());

        TestTime::addSeconds(9);
        $this->assertFalse($this->lock->get());

        TestTime::addSecond();
        $this->assertTrue($this->lock->get());
    }
}
