<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\EmailLists\Subscribers;

use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\Subscribers\SubscribersController;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class SubscribersControllerTest extends TestCase
{
    use RespondsToApiRequests;

    private EmailList $emailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();

        $this->emailList = EmailList::factory()->create();
    }

    /** @test */
    public function it_can_list_all_subscribers_of_an_email_list()
    {
        $subscribers = Subscriber::factory(3)->create([
            'email_list_id' => $this->emailList->id,
        ]);

        $response = $this
            ->getJson(action([SubscribersController::class, 'index'], $this->emailList->id))
            ->assertSuccessful()
            ->assertJsonCount(3, 'data');

        foreach ($subscribers as $subscriber) {
            $response->assertJsonFragment(['email' => $subscriber->email]);
        }
    }

    /** @test */
    public function it_can_filter_on_subscription_status()
    {
        /** @var Subscriber $subscriber */
        $subscriber = Subscriber::factory()->create([
            'email_list_id' => $this->emailList->id,
        ]);

        $endpoint = action([SubscribersController::class, 'index'], $this->emailList->id) . '?filter[status]=unsubscribed';

        $this
            ->getJson($endpoint)
            ->assertSuccessful()
            ->assertJsonCount(0, 'data');

        $subscriber->unsubscribe();

        $this
            ->getJson($endpoint)
            ->assertSuccessful()
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function it_can_show_a_subscriber()
    {
        /** @var Subscriber $subscriber */
        $subscriber = Subscriber::factory()->create();

        $this
            ->getJson(action([SubscribersController::class, 'show'], $subscriber))
            ->assertSuccessful()
            ->assertJsonFragment(['email' => $subscriber->email]);
    }

    /** @test */
    public function it_can_delete_a_subscriber()
    {
        /** @var Subscriber $subscriber */
        $subscriber = Subscriber::factory()->create();

        $this
            ->deleteJson(action([SubscribersController::class, 'destroy'], $subscriber))
            ->assertSuccessful();

        $this->assertCount(0, Subscriber::all());
    }

	/** @test */
	public function it_can_update_a_subscriber()
	{
		/** @var Subscriber $subscriber */
		$subscriber = Subscriber::factory()
			->for(EmailList::factory(), 'emailList')
			->create();

		$attributes = [
			'email' => 'janedoe@example.com',
			'first_name' => 'Jane',
			'last_name' => 'Doe',
			'tags' => ['test1', 'test2']
		];
		$this
			->patchJson(action([SubscribersController::class, 'update'], $subscriber), $attributes)
			->assertSuccessful();

		$subscriber->refresh();
		$this->assertEquals($attributes['email'], $subscriber->email);
		$this->assertEquals($attributes['first_name'], $subscriber->first_name);
		$this->assertEquals($attributes['last_name'], $subscriber->last_name);
		$this->assertEquals($attributes['tags'], $subscriber->tags->pluck('name')->toArray());
    }
}
