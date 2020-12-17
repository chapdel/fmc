<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\App\EmailLists;

use Spatie\Mailcoach\Http\App\Controllers\EmailLists\DestroyEmailListController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\EmailListsIndexController;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Tests\TestCase;

class DestroyEmailListControllerTest extends TestCase
{
    /** @test */
    public function it_can_delete_an_email_list()
    {
        $this->authenticate();

        $emailList = EmailList::factory()->create();

        $this
            ->delete(action(DestroyEmailListController::class, $emailList->id))
            ->assertRedirect(action(EmailListsIndexController::class));

        $this->assertCount(0, EmailList::get());
    }
}
