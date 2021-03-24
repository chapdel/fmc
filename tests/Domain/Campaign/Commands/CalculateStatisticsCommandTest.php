<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Commands;

use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Domain\Campaign\Commands\CalculateStatisticsCommand;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class CalculateStatisticsCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        TestTime::freeze('Y-m-d H:i:s', '2019-01-01 00:00:00');
    }

    /**
     * @test
     *
     * @dataProvider caseProvider
     */
    public function it_will_recalculate_statistics_at_the_right_time(
        Carbon $sentAt,
        ?Carbon $statisticsCalculatedAt,
        bool $jobShouldHaveBeenDispatched
    ) {
        Bus::fake();

        $campaign = Campaign::factory()->create([
            'sent_at' => $sentAt,
            'statistics_calculated_at' => $statisticsCalculatedAt,
        ]);

        $this->artisan(CalculateStatisticsCommand::class)->assertExitCode(0);

        $jobShouldHaveBeenDispatched
            ? Bus::assertDispatched(CalculateStatisticsJob::class)
            : Bus::assertNotDispatched(CalculateStatisticsJob::class);
    }

    public function caseProvider(): array
    {
        TestTime::freeze('Y-m-d H:i:s', '2019-01-01 00:00:00');

        return [
            [now()->subSecond(), null, true],

            [now()->subMinutes(2), now()->subMinute(), true],

            [now()->subMinutes(5), now()->subMinutes(1), true],
            [now()->subMinutes(6), now()->subMinutes(1), false],

            [now()->subMinutes(20), now()->subMinutes(9), false],
            [now()->subMinutes(20), now()->subMinutes(10)->subSecond(), true],

            [now()->subHours(3), now()->subHour(), false],
            [now()->subHours(3), now()->subHour()->subSecond(), true],

            [now()->subDay()->subMinute(), now()->subHours(4), false],
            [now()->subDay()->subMinute(), now()->subHours(4)->subSecond(), true],

            [now()->subWeeks(2), now()->subDay(), true],
            [now()->subWeeks(2)->subSecond(), now()->subDay(), false],


        ];
    }

    /** @test */
    public function it_can_recalculate_the_statistics_of_a_single_campaign()
    {
        $campaign = Campaign::factory()->create([
            'sent_at' => now()->subYear(),
            'statistics_calculated_at' => null,
        ]);

        $this->artisan(CalculateStatisticsCommand::class, ['campaignId' => $campaign->id])->assertExitCode(0);

        $this->assertNotNull($campaign->refresh()->statistics_calculated_at);
    }
}
