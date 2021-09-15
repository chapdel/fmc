<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Policies\EmailListPolicy;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\CreateEmailListController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Settings\EmailListGeneralSettingsController;
use Spatie\Mailcoach\Tests\TestClasses\CustomEmailListDenyAllPolicy;

it('can create a new email list', function () {
    test()->authenticate();

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
        ->assertRedirect(action([EmailListGeneralSettingsController::class, 'edit'], EmailList::first()->id));

    test()->assertDatabaseHas(static::getEmailListTableName(), $attributes);
});

it('sets mailers based on the mailcoach mailer config', function () {
    test()->authenticate();

    config()->set('mailcoach.mailer', 'some-mailer');

    $attributes = [
        'name' => 'new list',
        'default_from_email' => 'john@example.com',
    ];

    $this
        ->postJson(action(CreateEmailListController::class), $attributes);

    $attributes['transactional_mailer'] = 'some-mailer';
    $attributes['campaign_mailer'] = 'some-mailer';

    test()->assertDatabaseHas(static::getEmailListTableName(), $attributes);
});

it('sets mailers based on the config', function () {
    test()->authenticate();

    config()->set('mailcoach.mailer', 'some-mailer');
    config()->set('mailcoach.transactional.mailer', 'some-transactional-mailer');
    config()->set('mailcoach.campaigns.mailer', 'some-campaign-mailer');

    $attributes = [
        'name' => 'new list',
        'default_from_email' => 'john@example.com',
    ];

    test()->post(action(CreateEmailListController::class), $attributes);

    $attributes['transactional_mailer'] = 'some-transactional-mailer';
    $attributes['campaign_mailer'] = 'some-campaign-mailer';

    test()->assertDatabaseHas(static::getEmailListTableName(), $attributes);
});

it('authorizes access with custom policy', function () {
    app()->bind(EmailListPolicy::class, CustomEmailListDenyAllPolicy::class);

    test()->authenticate();

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
});
