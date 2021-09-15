<?php

use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Policies\EmailListPolicy;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\CreateEmailListController;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomEmailListDenyAllPolicy;

uses(TestCase::class);

beforeEach(function () {
    test()->emailList = EmailList::factory()->create();
});

it('uses default policy', function () {
    Gate::define('viewMailcoach', fn ($user) => $user->email === 'jane@example.com');
    $jane = (new User())->forceFill(['email' => 'jane@example.com']);
    $john = (new User())->forceFill(['email' => 'john@example.com']);

    test()->assertInstanceOf(EmailListPolicy::class, Gate::getPolicyFor(test()->emailList));
    test()->assertTrue($jane->can("create", EmailList::class));
    test()->assertFalse($john->can("create", EmailList::class));
});

it('uses custom policy', function () {
    Gate::define('viewMailcoach', fn ($user) => $user->email === 'jane@example.com');
    $jane = (new User())->forceFill(['email' => 'jane@example.com']);

    app()->bind(EmailListPolicy::class, CustomEmailListDenyAllPolicy::class);

    test()->assertInstanceOf(CustomEmailListDenyAllPolicy::class, Gate::getPolicyFor(test()->emailList));
    test()->assertFalse($jane->can("create", EmailList::class));

    $this
        ->postCreateList($jane)
        ->assertForbidden();
});

it('authorizes relevant routes', function () {
    Gate::define('viewMailcoach', fn ($user) => $user->email === 'jane@example.com');
    $jane = (new User())->forceFill(['email' => 'jane@example.com']);

    app()->bind(EmailListPolicy::class, CustomEmailListDenyAllPolicy::class);

    $this
        ->postCreateList($jane)
        ->assertForbidden();
});

// Helpers
function postCreateList(Authorizable $asUser)
{
    return $this
        ->withExceptionHandling()
        ->actingAs($asUser)
        ->post(action(CreateEmailListController::class), [
            'name' => 'new list',
            'default_from_email' => 'john@example.com',
        ]);
}
