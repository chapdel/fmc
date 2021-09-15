<?php

use Illuminate\Support\Facades\Date;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    Date::setTestNow('2020-08-12 09:17');

    test()->campaign = Campaign::factory()->create();
});

it('can be scheduled', function () {
    test()->assertNull(test()->campaign->scheduled_at);

    test()->campaign->scheduleToBeSentAt(now());

    test()->assertEquals('2020-08-12 09:17', test()->campaign->scheduled_at->toMailcoachFormat());
});

it('stores the date in utc', function () {
    config()->set('app.timezone', 'Europe/Brussels');

    test()->assertNull(test()->campaign->scheduled_at);

    test()->campaign->scheduleToBeSentAt(now()->setTimezone('Europe/Brussels'));

    test()->assertEquals('2020-08-12 11:17', test()->campaign->scheduled_at->toMailcoachFormat());
    test()->assertEquals('2020-08-12 09:17', test()->campaign->scheduled_at->format('Y-m-d H:i'));
});

it('can be marked as unscheduled', function () {
    test()->campaign->update(['scheduled_at' => now()]);

    test()->campaign->markAsUnscheduled();

    test()->assertNull(test()->campaign->scheduled_at);
});

it('scopes scheduled campaigns', function () {
    Campaign::factory()->create(['scheduled_at' => now()]);
    Campaign::factory()->create(['scheduled_at' => null]);

    test()->assertEquals(1, Campaign::scheduled()->count());
});

it('scopes should be sent campaigns', function () {
    Campaign::factory()->create(['scheduled_at' => now()->subDay()]);
    Campaign::factory()->create(['scheduled_at' => now()->addDay()]);

    test()->assertEquals(1, Campaign::shouldBeSentNow()->count());
});

it('scopes should be sent campaigns correctly when a timezone is set', function () {
    config()->set('app.timezone', 'America/Chicago');

    Campaign::factory()->create()->scheduleToBeSentAt(now()->setTimezone('America/Chicago'));
    Campaign::factory()->create()->scheduleToBeSentAt(now()->addDay()->setTimezone('America/Chicago'));

    test()->assertEquals(1, Campaign::shouldBeSentNow()->count());
});
