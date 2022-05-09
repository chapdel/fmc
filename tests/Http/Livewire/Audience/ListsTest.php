<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Policies\EmailListPolicy;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Livewire\Audience\Lists;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\Campaigns;
use Spatie\Mailcoach\Tests\TestClasses\CustomEmailListDenyAllPolicy;
use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->emailList = EmailList::factory()->create();
});

it('can delete an email list', function () {
    test()->authenticate();

    livewire(Lists::class)
        ->call('deleteList', $this->emailList->id);

    expect(EmailList::count())->toBe(0);
});

it('authorizes access with custom policy', function () {
    app()->bind(EmailListPolicy::class, CustomEmailListDenyAllPolicy::class);

    test()->authenticate();

    livewire(Lists::class)
        ->call('deleteList', $this->emailList->id);
})->throws(\Illuminate\Auth\Access\AuthorizationException::class);
