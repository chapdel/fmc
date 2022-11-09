<?php

use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;
use Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails\ShowTransactionalMailController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();
});

it('can show a transactional mail', function () {
    /** @var TransactionalMailLogItem $transactionalMail */
    $transactionalMail = TransactionalMailLogItem::factory()->create();

    $this
        ->get(action(ShowTransactionalMailController::class, $transactionalMail))
        ->assertSuccessful()
        ->assertJsonFragment([
            'subject' => $transactionalMail->subject,
        ]);
});
