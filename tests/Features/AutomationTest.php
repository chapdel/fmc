<?php

namespace Spatie\Mailcoach\Tests\Features;

use Carbon\CarbonInterval;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Automation\Models\Action;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\CampaignAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\EnsureTagsExistAction;
use Spatie\Mailcoach\Domain\Automation\Support\Actions\WaitAction;
use Spatie\Mailcoach\Domain\Automation\Support\Triggers\SubscribedAutomationTrigger;
use Spatie\Mailcoach\Tests\TestCase;

class AutomationTest extends TestCase
{
    /** @test * */
    public function it_can_create_a_complicated_automation()
    {
        $emailList = EmailList::factory()->create();

        $campaign1 = Campaign::factory()->create([
            'status' => CampaignStatus::AUTOMATED,
        ]);

        $tagsDontExist = Campaign::factory()->create([
            'status' => CampaignStatus::AUTOMATED,
        ]);

        Automation::create()
            ->name('Getting started with Mailcoach')
            ->to($emailList)
            ->trigger(new SubscribedAutomationTrigger())
            ->interval(CarbonInterval::minutes(10)) // Run through the automation and check actions every 10 min
            ->chain([
                new WaitAction(CarbonInterval::day()), // Wait one day
                new CampaignAction($campaign1), // Send first email
                new EnsureTagsExistAction(
                    checkFor: CarbonInterval::days(3), // Keep checking tags for 3 days, if not they get the default or halted
                    tags: [
                        [
                            'tag' => 'mc::campaign-1-clicked-1',
                            'actions' => [
                                new WaitAction(CarbonInterval::day()), // Wait one day
                                new CampaignAction($campaign1), // Send first email
                            ],
                        ],
                        [
                            'tag' => 'mc::campaign-1-opened',
                            'actions' => [
                                new WaitAction(CarbonInterval::day()), // Wait one day
                                new CampaignAction($campaign1), // Send first email
                            ]
                        ],
                    ],
                    defaultActions: [
                        new CampaignAction($tagsDontExist),
                    ],
                ),
                new WaitAction(CarbonInterval::days(3)), // Wait 3 days
                new CampaignAction($campaign1),
            ]);

        $this->assertEquals(1, Automation::count());
        tap(Automation::first(), function (Automation $automation) {
            $this->assertEquals(CarbonInterval::minutes(10), $automation->interval);
            $this->assertInstanceOf(SubscribedAutomationTrigger::class, $automation->trigger);
        });
        $this->assertEquals(10, Action::count());

        $this->assertEquals(2, Action::where('key', 'mc::campaign-1-clicked-1')->count());
        $this->assertEquals(2, Action::where('key', 'mc::campaign-1-opened')->count());
        $this->assertEquals(1, Action::where('key', 'default')->count());
    }
}
