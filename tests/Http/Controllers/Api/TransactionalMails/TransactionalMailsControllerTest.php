<?php

use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;
use Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails\TransactionalMailsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    TransactionalMailLogItem::factory()->count(2)->create()->each(fn ($item) => $item->contentItem->update(['subject' => 'foo']));
    TransactionalMailLogItem::factory()->count(2)->create()->each(fn ($item) => $item->contentItem->update(['subject' => 'bar']));
});

it('can show all transactional mails', function () {
    $transactionalMails = $this
        ->get(action(TransactionalMailsController::class))
        ->assertSuccessful()
        ->json('data');

    expect($transactionalMails)->toHaveCount(4);
});

it('can search mails with a certain subject', function () {
    $transactionalMails = $this
        ->get(action(TransactionalMailsController::class).'?filter[search]=ba')
        ->assertSuccessful()
        ->json('data');

    expect($transactionalMails)->toHaveCount(2);

    expect($transactionalMails[0]['subject'])->toEqual('bar');
});

it('can search mails by their send\'s transport_message_id', function () {
    Send::factory()->transactionalMailLogItem()->create([
        'transport_message_id' => 'uuid-1234',
    ]);

    $transactionalMails = $this
        ->get(action(TransactionalMailsController::class).'?filter[transport_message_id]=uuid-1234')
        ->assertSuccessful()
        ->json('data');

    expect($transactionalMails)->toHaveCount(1);
});
