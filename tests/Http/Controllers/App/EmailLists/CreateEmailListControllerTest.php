<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\App\EmailLists;

use Spatie\Mailcoach\Domain\Audience\Policies\EmailListPolicy;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\CreateEmailListController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Settings\EmailListGeneralSettingsController;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomEmailListDenyAllPolicy;

class CreateEmailListControllerTest extends TestCase
{
    /** @test */
    public function it_can_create_a_new_email_list()
    {
        $this->authenticate();

        $attributes = [
            'name' => 'new list',
            'default_from_email' => 'john@example.com',
        ];

        $this
            ->post(
                action(CreateEmailListController::class),
                $attributes
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(action([EmailListGeneralSettingsController::class, 'edit'], 1));

        $this->assertDatabaseHas('mailcoach_email_lists', $attributes);
    }

    /** @test */
    public function it_sets_mailers_based_on_the_mailcoach_mailer_config()
    {
        $this->authenticate();

        config()->set('mailcoach.mailer', 'some-mailer');

        $attributes = [
            'name' => 'new list',
            'default_from_email' => 'john@example.com',
        ];

        $this
            ->postJson(action(CreateEmailListController::class), $attributes);

        $attributes['transactional_mailer'] = 'some-mailer';
        $attributes['campaign_mailer'] = 'some-mailer';

        $this->assertDatabaseHas('mailcoach_email_lists', $attributes);
    }

    /** @test */
    public function it_sets_mailers_based_on_the_config()
    {
        $this->authenticate();

        config()->set('mailcoach.mailer', 'some-mailer');
        config()->set('mailcoach.transactional.mailer', 'some-transactional-mailer');
        config()->set('mailcoach.campaigns.mailer', 'some-campaign-mailer');

        $attributes = [
            'name' => 'new list',
            'default_from_email' => 'john@example.com',
        ];

        $this->post(action(CreateEmailListController::class), $attributes);

        $attributes['transactional_mailer'] = 'some-transactional-mailer';
        $attributes['campaign_mailer'] = 'some-campaign-mailer';

        $this->assertDatabaseHas('mailcoach_email_lists', $attributes);
    }

    /** @test */
    public function it_authorizes_access_with_custom_policy()
    {
        app()->bind(EmailListPolicy::class, CustomEmailListDenyAllPolicy::class);

        $this->authenticate();

        $attributes = [
            'name' => 'new list',
            'default_from_email' => 'john@example.com',
        ];

        $this
            ->withExceptionHandling()
            ->post(
                action(CreateEmailListController::class),
                $attributes
            )
            ->assertForbidden();
    }
}
