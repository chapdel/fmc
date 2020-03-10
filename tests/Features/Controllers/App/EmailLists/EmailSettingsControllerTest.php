<?php

namespace Spatie\Mailcoach\Tests\Feature\Controllers\App\EmailLists;

use Spatie\Mailcoach\Http\App\Controllers\EmailLists\EmailListSettingsController;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Tests\TestCase;

class EmailSettingsControllerTest extends TestCase
{
    /** @test */
    public function it_can_update_the_settings_of_an_email_list()
    {
        $this->authenticate();

        $emailList = EmailList::create([
            'name' => 'my list',
            'campaign_mailer' => 'array',
            'transactional_mailer' => 'array',
        ]);

        $attributes = [
            'name' => 'updated name',
            'default_from_email' => 'jane@example.com',
            'default_from_name' => 'Jane Doe',
            'campaign_mailer' => 'log',
            'transactional_mailer' => 'log',
        ];

        $this
            ->put(
                action([EmailListSettingsController::class, 'update'], $emailList->id),
                $attributes
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(action([EmailListSettingsController::class, 'edit'], $emailList->id));

        $this->assertDatabaseHas('mailcoach_email_lists', $attributes);
    }
}
