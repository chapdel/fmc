<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\TransactionalMails;

use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails\ShowTransactionalMailController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class ShowTransactionalMailControllerTest extends TestCase
{
    use RespondsToApiRequests;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();
    }

    /** @test */
    public function it_can_show_a_transactional_mail()
    {
        /** @var TransactionalMail $transactionalMail */
        $transactionalMail = TransactionalMail::factory()->create();

        $this
            ->get(action(ShowTransactionalMailController::class, $transactionalMail))
            ->assertSuccessful()
            ->assertJsonFragment([
                'subject' => $transactionalMail->subject,
            ]);
    }
}
