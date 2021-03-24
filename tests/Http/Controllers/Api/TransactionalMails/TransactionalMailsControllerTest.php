<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\TransactionalMails;

use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails\TransactionalMailsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class TransactionalMailsControllerTest extends TestCase
{
    use RespondsToApiRequests;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();

        TransactionalMail::factory()->count(2)->create(['subject' => 'foo']);
        TransactionalMail::factory()->count(2)->create(['subject' => 'bar']);
    }

    /** @test */
    public function it_can_show_all_transactional_mails()
    {
        $transactionalMails = $this
            ->get(action(TransactionalMailsController::class))
            ->assertSuccessful()
            ->json('data');

        $this->assertCount(4, $transactionalMails);
    }

    /** @test */
    public function it_can_search__mails_with_a_certain_subject()
    {
        $transactionalMails = $this
                    ->get(action(TransactionalMailsController::class). '?filter[search]=ba')
                    ->assertSuccessful()
                    ->json('data');

        $this->assertCount(2, $transactionalMails);

        $this->assertEquals('bar', $transactionalMails[0]['subject']);
    }
}
