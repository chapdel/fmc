<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\App\Subscribers;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\DestroySubscriberController;
use Spatie\Mailcoach\Tests\TestCase;

class DestroySubscriberControllerTest extends TestCase
{
    /** @test */
    public function it_can_delete_a_subscriber()
    {
        $this->authenticate();

        $subscriber = Subscriber::factory()->create();

        $this
            ->delete(action(DestroySubscriberController::class, [$subscriber->emailList->id, $subscriber->id]))
            ->assertRedirect();

        $this->assertCount(0, Subscriber::get());
    }
}
