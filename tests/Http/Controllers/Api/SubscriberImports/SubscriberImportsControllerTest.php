<?php

use Spatie\Mailcoach\Domain\Audience\Enums\SubscriberImportStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Http\Api\Controllers\SubscriberImports\SubscriberImportsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    test()->emailList = EmailList::factory()->create();
});

it('can list all subscriber imports', function () {
    $subscriberImports = SubscriberImport::factory(3)->create();

    $response = $this
        ->getJson(action([SubscriberImportsController::class, 'index']))
        ->assertSuccessful()
        ->assertJsonCount(3, 'data');

    foreach ($subscriberImports as $subscriberImport) {
        $response->assertJsonFragment(['uuid' => $subscriberImport->uuid]);
    }
});

it('can show a subscriber import', function () {
    $subscriberImport = SubscriberImport::factory()->create();

    $this
        ->getJson(action([SubscriberImportsController::class, 'show'], $subscriberImport->uuid))
        ->assertSuccessful();
});

it('can create a subscriber import', function () {
    $payload = [
        'subscribers_csv' => 'email' .PHP_EOL . 'john@example.com',
        'email_list_id' => test()->emailList->id,
    ];

    $this
        ->postJson(action([SubscriberImportsController::class, 'store']), $payload)
        ->assertSuccessful();

    $payload['status'] = SubscriberImportStatus::Draft;

    test()->assertDatabaseHas('mailcoach_subscriber_imports', $payload);
});

it('can update a subscriber import', function () {
    $subscriberImport = SubscriberImport::factory()->create([
        'status' => SubscriberImportStatus::Draft,
    ]);

    $payload = [
        'subscribers_csv' => 'email' .PHP_EOL . 'john@example.com',
        'email_list_id' => test()->emailList->id,
    ];

    $this
        ->putJson(action([SubscriberImportsController::class, 'update'], $subscriberImport->uuid), $payload)
        ->assertSuccessful();

    test()->assertDatabaseHas('mailcoach_subscriber_imports', $payload);
});

it('can delete a subscriber import', function () {
    $subscriberImport = SubscriberImport::factory()->create();

    $this
        ->deleteJson(action([SubscriberImportsController::class, 'destroy'], $subscriberImport->uuid))
        ->assertSuccessful();

    expect(SubscriberImport::get())->toHaveCount(0);
});
