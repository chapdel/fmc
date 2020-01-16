<?php

namespace Spatie\Mailcoach\Tests\Feature\Controllers\App\EmailLists;

use Spatie\Mailcoach\Http\App\Controllers\EmailLists\CreateEmailListController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\SubscribersIndexController;
use Spatie\Mailcoach\Tests\TestCase;

class CreateEmailListControllerTest extends TestCase
{
    /** @test */
    public function it_can_create_a_new_email_list()
    {
        $this->authenticate();

        $attributes = [
            'name' => 'new list',
        ];

        $this
            ->post(
                action(CreateEmailListController::class),
                $attributes
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(action(SubscribersIndexController::class, 1));

        $this->assertDatabaseHas('mailcoach_email_lists', $attributes);
    }
}
