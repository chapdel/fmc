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

    /** @test */
    public function it_sets_mailers_based_on_the_mailcoach_mailer_config()
    {
        $this->authenticate();

        config()->set('mailcoach.mailer', 'some-mailer');

        $attributes = [
            'name' => 'new list',
        ];

        $this->post(action(CreateEmailListController::class), $attributes);

        $attributes['transactional_mailer'] = 'some-mailer';
        $attributes['campaign_mailer'] = 'some-mailer';

        $this->assertDatabaseHas('mailcoach_email_lists', $attributes);
    }

    /** @test */
    public function it_sets_mailers_based_on_the_config()
    {
        $this->authenticate();

        config()->set('mailcoach.mailer', 'some-mailer');
        config()->set('mailcoach.transactional_mailer', 'some-transactional-mailer');
        config()->set('mailcoach.campaign_mailer', 'some-campaign-mailer');

        $attributes = [
            'name' => 'new list',
        ];

        $this->post(action(CreateEmailListController::class), $attributes);

        $attributes['transactional_mailer'] = 'some-transactional-mailer';
        $attributes['campaign_mailer'] = 'some-campaign-mailer';

        $this->assertDatabaseHas('mailcoach_email_lists', $attributes);
    }
}
