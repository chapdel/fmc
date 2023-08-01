<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Policies\EmailListPolicy;
use Spatie\Mailcoach\Livewire\Audience\ListsComponent;
use Spatie\Mailcoach\Tests\TestClasses\CustomEmailListDenyAllPolicy;

beforeEach(function () {
    $this->emailList = EmailList::factory()->create();
});

it('can delete an email list', function () {
    test()->authenticate();

    Subscriber::factory()->create([
        'email_list_id' => $this->emailList->id,
    ]);

    expect(Subscriber::count())->toBe(1);

    Livewire::test(ListsComponent::class)
        ->callTableAction('Delete', $this->emailList);

    expect(Subscriber::count())->toBe(0);
    expect(EmailList::count())->toBe(0);
});

it('authorizes access with custom policy', function () {
    app()->bind(EmailListPolicy::class, CustomEmailListDenyAllPolicy::class);

    test()->authenticate();

    Livewire::test(ListsComponent::class)
        ->callTableAction('Delete', $this->emailList);
})->throws(\Illuminate\Auth\Access\AuthorizationException::class);
