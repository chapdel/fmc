<?php

use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports\StartSubscriberImportController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    test()->subscriberImport = SubscriberImport::factory()->create([
        'status' => SubscriberImportStatus::Draft,
        'subscribers_csv' => 'email' . PHP_EOL . 'john@example.com',
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
