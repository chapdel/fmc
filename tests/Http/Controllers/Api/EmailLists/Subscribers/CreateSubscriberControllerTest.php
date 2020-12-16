<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\EmailLists\Subscribers;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Campaign\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\SubscribersController;
use Spatie\Mailcoach\Domain\Campaign\Mails\ConfirmSubscriberMail;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class CreateSubscriberControllerTest extends TestCase
{
    use RespondsToApiRequests;

    private EmailList $emailList;

    private array $attributes;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();

        $this->emailList = EmailList::factory()->create([
            'requires_confirmation' => true,
        ]);

        $this->attributes = [
            'email_list_id' => $this->emailList->id,
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ];

        Mail::fake();
    }

    /** @test */
    public function it_can_create_a_subscriber_using_the_api()
    {
        $this
            ->postJson(action([SubscribersController::class, 'store'], $this->emailList), $this->attributes)
            ->assertSuccessful();

        $this->assertDatabaseHas('mailcoach_subscribers', $this->attributes);

        $this->assertEquals(SubscriptionStatus::UNCONFIRMED, Subscriber::first()->status);

        Mail::assertQueued(ConfirmSubscriberMail::class);
    }

    /** @test */
    public function it_can_skip_the_confirmation_while_subscribing()
    {
        $attributes = $this->attributes;

        $attributes['skip_confirmation'] = true;

        $this
            ->postJson(action([SubscribersController::class, 'store'], $this->emailList), $attributes)
            ->assertSuccessful();

        $this->assertEquals(SubscriptionStatus::SUBSCRIBED, Subscriber::first()->status);

        Mail::assertNotQueued(ConfirmSubscriberMail::class);
    }

    /** @test */
    public function it_can_create_a_subscriber_with_extra_attributes()
    {
        $this->attributes['extra_attributes'] = [
            'foo' => 'bar',
        ];

        $this
            ->postJson(action([SubscribersController::class, 'store'], $this->emailList), $this->attributes)
            ->assertSuccessful();

        $this->assertSame($this->attributes['extra_attributes'], Subscriber::first()->extra_attributes->toArray());
    }

    /** @test */
    public function it_can_create_a_subscriber_with_tags()
    {
        $this->attributes['tags'] = ['foo', 'bar'];

        $response = $this
            ->postJson(action([SubscribersController::class, 'store'], $this->emailList), $this->attributes);

        $response->assertSuccessful();
        $response->assertJsonFragment(['tags' => ['foo', 'bar']]);
        $this->assertTrue(Subscriber::first()->hasTag('foo'));
        $this->assertTrue(Subscriber::first()->hasTag('bar'));
    }
}
