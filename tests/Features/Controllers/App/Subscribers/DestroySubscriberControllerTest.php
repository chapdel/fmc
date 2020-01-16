<?php

namespace Spatie\Mailcoach\Tests\Feature\Controllers\App\Subscribers;

use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\DestroySubscriberController;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;

class DestroySubscriberControllerTest extends TestCase
{
    /** @test */
    public function it_can_delete_a_subscriber()
    {
        $this->authenticate();

        $subscriber = factory(Subscriber::class)->create();

        $this
            ->delete(action(DestroySubscriberController::class, [$subscriber->emailList->id, $subscriber->id]))
            ->assertRedirect();

        $this->assertCount(0, Subscriber::get());
    }
}
