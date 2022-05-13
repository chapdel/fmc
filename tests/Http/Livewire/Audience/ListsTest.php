<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Policies\EmailListPolicy;
use Spatie\Mailcoach\Http\App\Livewire\Audience\Lists;
use Spatie\Mailcoach\Tests\TestClasses\CustomEmailListDenyAllPolicy;

beforeEach(function () {
    $this->emailList = EmailList::factory()->create();
});

it('can delete an email list', function () {
    test()->authenticate();

    Livewire::test(Lists::class)
        ->call('deleteList', $this->emailList->id);

    expect(EmailList::count())->toBe(0);
});

it('authorizes access with custom policy', function () {
    app()->bind(EmailListPolicy::class, CustomEmailListDenyAllPolicy::class);

    test()->authenticate();

    Livewire::test(Lists::class)
        ->call('deleteList', $this->emailList->id);
})->throws(\Illuminate\Auth\Access\AuthorizationException::class);
