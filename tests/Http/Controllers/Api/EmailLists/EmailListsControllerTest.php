<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\EmailLists;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\EmailListsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class EmailListsControllerTest extends TestCase
{
    use RespondsToApiRequests;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();
    }

    /** @test */
    public function it_can_list_all_email_lists()
    {
        $emailLists = EmailList::factory(3)->create();

        $this
            ->getJson(action([EmailListsController::class, 'index']))
            ->assertSuccessful()
            ->assertSeeText($emailLists->first()->name);
    }

    /** @test */
    public function it_can_search_email_lists()
    {
        EmailList::factory()->create([
            'name' => 'one',
        ]);

        EmailList::factory()->create([
            'name' => 'two',
        ]);

        $this
            ->getJson(action([EmailListsController::class, 'index']) . '?filter[search]=two')
            ->assertSuccessful()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'two']);
    }

    /** @test */
    public function the_api_can_show_an_email_list()
    {
        $emailList = EmailList::factory()->create();

        $this
            ->getJson(action([EmailListsController::class, 'show'], $emailList))
            ->assertSuccessful()
            ->assertJsonFragment(['name' => $emailList->name]);
    }

    /** @test */
    public function an_email_list_can_be_stored_using_the_api()
    {
        $attributes = [
            'name' => 'email list name',
            'default_from_email' => 'johndoe@example.com',
            'default_from_name' => 'john doe',
            'default_reply_to_email' => 'johndoe@example.com',
            'default_reply_to_name' => 'john doe',
        ];

        $this
            ->postJson(action([EmailListsController::class, 'store'], $attributes))
            ->assertSuccessful();

        $this->assertDatabaseHas('mailcoach_email_lists', $attributes);
    }

    /** @test */
    public function an_email_list_can_be_updated_using_the_api()
    {
        $emailList = EmailList::factory()->create();

        $attributes = [
            'name' => 'email list name',
            'default_from_email' => 'johndoe@example.com',
            'default_from_name' => 'john doe',
            'default_reply_to_email' => 'johndoe@example.com',
            'default_reply_to_name' => 'john doe',
        ];

        $this
            ->putJson(action([EmailListsController::class, 'update'], $emailList), $attributes)
            ->assertSuccessful();

        $emailList = $emailList->refresh();

        $this->assertEquals(1, $emailList->id);
        $this->assertEquals($attributes['name'], $emailList->name);
        $this->assertEquals($attributes['default_from_email'], $emailList->default_from_email);
        $this->assertEquals($attributes['default_from_name'], $emailList->default_from_name);
        $this->assertEquals($attributes['default_reply_to_email'], $emailList->default_reply_to_email);
        $this->assertEquals($attributes['default_reply_to_name'], $emailList->default_reply_to_name);
    }

    /** @test */
    public function an_email_list_can_be_deleted_using_the_api()
    {
        $template = EmailList::factory()->create();

        $this
            ->deleteJson(action([EmailListsController::class, 'destroy'], $template))
            ->assertSuccessful();

        $this->assertCount(0, EmailList::get());
    }
}
