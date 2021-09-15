<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\ResendTransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails\ResendTransactionalMailController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();
});

it('can resend a mail', function () {
    Mail::fake();

    /** @var TransactionalMail $transactionalMail */
    $transactionalMail = TransactionalMail::factory()->create();

    $this
        ->post(action(ResendTransactionalMailController::class, $transactionalMail))
        ->assertSuccessful();

    Mail::assertSent(ResendTransactionalMail::class);
});
