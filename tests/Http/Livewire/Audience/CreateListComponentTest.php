<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Policies\EmailListPolicy;
use Spatie\Mailcoach\Http\App\Livewire\Audience\CreateListComponent;
use Spatie\Mailcoach\Tests\TestClasses\CustomEmailListDenyAllPolicy;

beforeEach(function () {
    test()->authenticate();
});

it('can create a list', function () {
    Livewire::test(CreateListComponent::class)
        ->set('name', 'My list')
        ->set('default_from_email', 'john@example.com')
        ->call('saveList')
        ->assertRedirect(route('mailcoach.emailLists.general-settings', EmailList::first()));

    test()->assertDatabaseHas(static::getEmailListTableName(), ['name' => 'My list', 'requires_confirmation' => true, 'campaigns_feed_enabled' => false]);
});

it('sets mailers based on the mailcoach mailer config', function () {
    test()->authenticate();

    config()->set('mailcoach.mailer', 'some-mailer');

    Livewire::test(CreateListComponent::class)
        ->set('name', 'new list')
        ->set('default_from_email', 'john@example.com')
        ->call('saveList');

    $attributes['transactional_mailer'] = 'some-mailer';
    $attributes['campaign_mailer'] = 'some-mailer';

    test()->assertDatabaseHas(static::getEmailListTableName(), $attributes);
});

it('authorizes access with custom policy', function () {
    app()->bind(EmailListPolicy::class, CustomEmailListDenyAllPolicy::class);

    test()->authenticate();

    Livewire::test(CreateListComponent::class)
        ->set('name', 'new list')
        ->set('default_from_email', 'john@example.com')
        ->call('saveList');
})->throws(\Illuminate\Auth\Access\AuthorizationException::class);
