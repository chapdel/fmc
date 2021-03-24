<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\TransactionalMails;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\ResendTransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails\ResendTransactionalMailController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class ResendTransactionMailControllerTest extends TestCase
{
    use RespondsToApiRequests;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();
    }

    /** @test */
    public function it_can_resend_a_mail()
    {
        Mail::fake();

        /** @var TransactionalMail $transactionalMail */
        $transactionalMail = TransactionalMail::factory()->create();

        $this
            ->post(action(ResendTransactionalMailController::class, $transactionalMail))
            ->assertSuccessful();

        Mail::assertSent(ResendTransactionalMail::class);
    }
}
