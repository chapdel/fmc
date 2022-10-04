<?php

use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Policies\EmailListPolicy;
use Spatie\Mailcoach\Http\App\Livewire\Audience\CreateListComponent;
use Spatie\Mailcoach\Tests\TestClasses\CustomEmailListDenyAllPolicy;

beforeEach(function () {
    test()->emailList = EmailList::factory()->create();
});

it('uses default policy', function () {
    Gate::define('viewMailcoach', fn ($user) => $user->email === 'jane@example.com');
    $jane = (new User())->forceFill(['email' => 'jane@example.com']);
    $john = (new User())->forceFill(['email' => 'john@example.com']);

    expect(Gate::getPolicyFor(test()->emailList))->toBeInstanceOf(EmailListPolicy::class);
    expect($jane->can('create', EmailList::class))->toBeTrue();
    expect($john->can('create', EmailList::class))->toBeFalse();
});

it('uses custom policy', function () {
    Gate::define('viewMailcoach', fn ($user) => $user->email === 'jane@example.com');
    $jane = (new User())->forceFill(['email' => 'jane@example.com']);

    app()->bind(EmailListPolicy::class, CustomEmailListDenyAllPolicy::class);

    expect(Gate::getPolicyFor(test()->emailList))->toBeInstanceOf(CustomEmailListDenyAllPolicy::class);
    expect($jane->can('create', EmailList::class))->toBeFalse();

    postCreateList($jane);
})->throws(\Illuminate\Auth\Access\AuthorizationException::class);

it('authorizes relevant routes', function () {
    Gate::define('viewMailcoach', fn ($user) => $user->email === 'jane@example.com');
    $jane = (new User())->forceFill(['email' => 'jane@example.com']);

    app()->bind(EmailListPolicy::class, CustomEmailListDenyAllPolicy::class);

    postCreateList($jane);
})->throws(\Illuminate\Auth\Access\AuthorizationException::class);

// Helpers
function postCreateList(Authorizable $asUser)
{
    test()->actingAs($asUser);

    return Livewire::test(CreateListComponent::class)
        ->set('name', 'new list')
        ->set('default_from_email', 'john@example.com')
        ->call('saveList');
}
