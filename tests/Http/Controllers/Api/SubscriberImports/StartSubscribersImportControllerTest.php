<?php

use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports\StartSubscriberImportController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);
uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    test()->subscriberImport = SubscriberImport::factory()->create([
        'status' => SubscriberImportStatus::DRAFT,
        'subscribers_csv' => 'email' . PHP_EOL . 'john@example.com',
    ]);
});

test('the import can be started via the api', function () {
    $this
        ->postJson(action(StartSubscriberImportController::class, test()->subscriberImport))
        ->assertSuccessful();

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */
    $emailList = test()->subscriberImport->emailList;

    test()->assertTrue($emailList->isSubscribed('john@example.com'));
    test()->assertEquals(SubscriberImportStatus::COMPLETED, test()->subscriberImport->refresh()->status);
});
