<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Policies\EmailListPolicy;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Settings\EmailListGeneralSettingsController;
use Spatie\Mailcoach\Http\App\Livewire\Audience\CreateList;
use Spatie\Mailcoach\Http\App\Livewire\Audience\CreateSubscriber;
use Spatie\Mailcoach\Tests\TestClasses\CustomEmailListDenyAllPolicy;
use function Pest\Livewire\livewire;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\Draft\CampaignSettingsController;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CreateCampaign;

it('can create a subscriber', function () {
    test()->authenticate();

    /** @var EmailList $emailList */
    $emailList = EmailList::factory()->create();

    Livewire::test(CreateSubscriber::class, ['emailList' => $emailList])
        ->set('email', 'john@example.com')
        ->set('first_name', 'John')
        ->set('last_name', 'Doe')
        ->call('saveSubscriber');

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
    $subscriber = Subscriber::first();

    expect($subscriber->email)->toEqual('john@example.com');
    expect($subscriber->first_name)->toEqual('John');
    expect($subscriber->last_name)->toEqual('Doe');

    expect($emailList->isSubscribed($subscriber->email))->toBeTrue();
});
