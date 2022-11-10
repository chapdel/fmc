<?php

use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;
use Spatie\Mailcoach\Http\Api\Controllers\SendsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();
});

it('can list all sends', function () {
    $sends = Send::factory(3)->create();

    $this
        ->getJson(action([SendsController::class, 'index']))
        ->assertSuccessful()
        ->assertSeeText($sends->first()->uuid);
});

it('can filter by subscriber_uuid', function () {
    $sends = Send::factory(3)->create();

    $this
        ->getJson(action([SendsController::class, 'index']).'?filter[subscriber_uuid]='.$sends->first()->subscriber->uuid)
        ->assertSuccessful()
        ->assertJsonCount(1, 'data');
});

it('can filter by campaign uuid', function () {
    $sends = Send::factory(3)->create();

    $this
        ->getJson(action([SendsController::class, 'index']).'?filter[campaign_uuid]='.$sends->first()->campaign->uuid)
        ->assertSuccessful()
        ->assertJsonCount(1, 'data');
});

it('can filter by automationMail uuid', function () {
    $sends = Send::factory(3)->create();

    $this
        ->getJson(action([SendsController::class, 'index']).'?filter[automation_mail_uuid]='.$sends->first()->automationMail->uuid)
        ->assertSuccessful()
        ->assertJsonCount(1, 'data');
});

it('can filter by transactionalMailLogItem uuid', function () {
    $sends = Send::factory(3)->create([
        'transactional_mail_log_item_id' => TransactionalMailLogItem::factory(),
    ]);

    $this
        ->getJson(action([SendsController::class, 'index']).'?filter[transactional_mail_log_item_uuid]='.$sends->first()->transactionalMailLogItem->uuid)
        ->assertSuccessful()
        ->assertJsonCount(1, 'data');
});

test('the api can show a send', function () {
    /** @var Send $send */
    $send = Send::factory()->create();

    $this
        ->getJson(action([SendsController::class, 'show'], $send->uuid))
        ->assertSuccessful()
        ->assertJsonFragment(['uuid' => $send->uuid]);
});

test('a send can be deleted using the api', function () {
    $send = Send::factory()->create();

    $this
        ->deleteJson(action([SendsController::class, 'destroy'], $send->uuid))
        ->assertSuccessful();

    expect(Send::get())->toHaveCount(0);
});
