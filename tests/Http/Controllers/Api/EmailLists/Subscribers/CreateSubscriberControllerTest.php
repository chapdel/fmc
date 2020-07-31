<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\EmailLists\Subscribers;

use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\SubscribersController;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class CreateSubscriberControllerTest extends TestCase
{
    use RespondsToApiRequests;

    private EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();

        $this->emailList = factory(EmailList::class)->create();
    }

    /** @test */
    public function it_can_create_a_subscriber_using_the_api()
    {
        $attributes = [
            'email_list_id' => $this->emailList->id,
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];

        $this
            ->postJson(action([SubscribersController::class, 'store'], $this->emailList), $attributes)
            ->assertSuccessful();

        $this->assertDatabaseHas('mailcoach_subscribers', $attributes);
    }
}
