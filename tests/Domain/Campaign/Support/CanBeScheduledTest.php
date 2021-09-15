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
    expect(test()->campaign->scheduled_at)->toBeNull();

    test()->campaign->scheduleToBeSentAt(now());

    expect(test()->campaign->scheduled_at->toMailcoachFormat())->toEqual('2020-08-12 09:17');
});

it('stores the date in utc', function () {
    config()->set('app.timezone', 'Europe/Brussels');

    expect(test()->campaign->scheduled_at)->toBeNull();

    test()->campaign->scheduleToBeSentAt(now()->setTimezone('Europe/Brussels'));

    expect(test()->campaign->scheduled_at->toMailcoachFormat())->toEqual('2020-08-12 11:17');
    expect(test()->campaign->scheduled_at->format('Y-m-d H:i'))->toEqual('2020-08-12 09:17');
});

it('can be marked as unscheduled', function () {
    test()->campaign->update(['scheduled_at' => now()]);

    test()->campaign->markAsUnscheduled();

    expect(test()->campaign->scheduled_at)->toBeNull();
});

it('scopes scheduled campaigns', function () {
    Campaign::factory()->create(['scheduled_at' => now()]);
    Campaign::factory()->create(['scheduled_at' => null]);

    expect(Campaign::scheduled()->count())->toEqual(1);
});

it('scopes should be sent campaigns', function () {
    Campaign::factory()->create(['scheduled_at' => now()->subDay()]);
    Campaign::factory()->create(['scheduled_at' => now()->addDay()]);

    expect(Campaign::shouldBeSentNow()->count())->toEqual(1);
});

it('scopes should be sent campaigns correctly when a timezone is set', function () {
    config()->set('app.timezone', 'America/Chicago');

    Campaign::factory()->create()->scheduleToBeSentAt(now()->setTimezone('America/Chicago'));
    Campaign::factory()->create()->scheduleToBeSentAt(now()->addDay()->setTimezone('America/Chicago'));

    expect(Campaign::shouldBeSentNow()->count())->toEqual(1);
});
