<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Policies\EmailListPolicy;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\DestroyEmailListController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\EmailListsIndexController;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomEmailListDenyAllPolicy;

uses(TestCase::class);

it('can delete an email list', function () {
    test()->authenticate();

    $emailList = EmailList::factory()->create();

    $this
        ->delete(action(DestroyEmailListController::class, $emailList->id))
        ->assertRedirect(action(EmailListsIndexController::class));

    expect(EmailList::get())->toHaveCount(0);
});

it('authorizes access with custom policy', function () {
    app()->bind(EmailListPolicy::class, CustomEmailListDenyAllPolicy::class);

    test()->authenticate();

    $emailList = EmailList::factory()->create();

    $this
        ->withExceptionHandling()
        ->delete(action(DestroyEmailListController::class, $emailList->id))
        ->assertForbidden();
});
