<?php

use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports\AppendSubscriberImportController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();
});

it('can append to the subscribers csv', function () {
    $initialSubscribersCsv = 'email' . PHP_EOL . 'john@example.com';

    $subscriberImport = SubscriberImport::factory()->create([
       'subscribers_csv' => $initialSubscribersCsv,
    ]);

    $payload = [
        'subscribers_csv' => 'paul@example.com',
    ];

    $this
        ->postJson(action(AppendSubscriberImportController::class, $subscriberImport), $payload)
        ->assertSuccessful();

    $expected = $initialSubscribersCsv . PHP_EOL . $payload['subscribers_csv'];

    expect($subscriberImport->refresh()->subscribers_csv)->toEqual($expected);
});
