<?php

use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Domain\Audience\Policies\SubscriberImportPolicy;
use Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports\StartSubscriberImportController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestClasses\CustomSubscriberImportDenyAllPolicy;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    test()->subscriberImport = SubscriberImport::factory()->create([
        'status' => SubscriberImportStatus::Draft,
        'subscribers_csv' => 'email'.PHP_EOL.'john@example.com',
    ]);
});

test('the import can be started via the api', function () {
    $this
        ->postJson(action(StartSubscriberImportController::class, test()->subscriberImport))
        ->assertSuccessful();

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */
    $emailList = test()->subscriberImport->emailList;

    expect($emailList->isSubscribed('john@example.com'))->toBeTrue();
    expect(test()->subscriberImport->refresh()->status)->toEqual(SubscriberImportStatus::Completed);
});

test('it returns validation error when invalid csv', function () {
    $import = SubscriberImport::factory()->create([
        'status' => SubscriberImportStatus::Draft,
        'subscribers_csv' => 'jane@example.com'.PHP_EOL.'john@example.com',
    ]);

    $this
        ->postJson(action(StartSubscriberImportController::class, $import))
        ->assertJsonValidationErrors('file');
});

test('it checks a policy', function () {
    $this->withExceptionHandling();

    $import = SubscriberImport::factory()->create([
        'status' => SubscriberImportStatus::Draft,
        'subscribers_csv' => 'email'.PHP_EOL.'john@example.com',
    ]);

    app()->bind(SubscriberImportPolicy::class, CustomSubscriberImportDenyAllPolicy::class);

    $this
        ->postJson(action(StartSubscriberImportController::class, $import))
        ->assertForbidden();
});
