<?php

use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails\ShowTransactionalMailController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();
});

it('can show a transactional mail', function () {
    /** @var TransactionalMail $transactionalMail */
    $transactionalMail = TransactionalMail::factory()->create();

    $this
        ->get(action(ShowTransactionalMailController::class, $transactionalMail))
        ->assertSuccessful()
        ->assertJsonFragment([
            'subject' => $transactionalMail->subject,
        ]);
});
