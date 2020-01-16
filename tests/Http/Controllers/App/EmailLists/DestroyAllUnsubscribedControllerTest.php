<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\App\EmailLists;

use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Tests\TestCase;

class DestroyAllUnsubscribedControllerTest extends TestCase
{
    /** @test */
    public function it_will_delete_all_unsubscribers()
    {
        $this->authenticate();

        $emailList = factory(EmailList::class)->create(['requires_confirmation' => false]);
        $anotherEmailList = factory(EmailList::class)->create(['requires_confirmation' => false]);

        $subscriber = Subscriber::createWithEmail('subscribed@example.com')->subscribeTo($emailList);

        $unsubscribedSubscriber = Subscriber::createWithEmail('unsubscribed@example.com')
            ->subscribeTo($emailList)
            ->unsubscribe();

        $unsubscribedSubscriberOfAnotherList = Subscriber::createWithEmail('unsubscribed-other-list@example.com')
            ->subscribeTo($anotherEmailList)
            ->unsubscribe();

        $this
            ->delete(route('mailcoach.emailLists.destroy-unsubscribes', $emailList->refresh()))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $existingSubscriberIds = Subscriber::pluck('id')->toArray();

        $this->assertTrue(in_array($subscriber->id, $existingSubscriberIds));
        $this->assertFalse(in_array($unsubscribedSubscriber->id, $existingSubscriberIds));
        $this->assertTrue(in_array($unsubscribedSubscriberOfAnotherList->id, $existingSubscriberIds));
    }
}
