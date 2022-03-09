<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Http\Front\Controllers\UnsubscribeController;

beforeEach(function () {
    test()->withExceptionHandling();

    test()->emailList = EmailList::factory()->create();

    test()->email = 'info@spatie.be';

    test()->subscriber = test()->emailList->subscribe(test()->email);
});

it('can load the unsubscribe url without a send', function () {
    $this
        ->get(action([UnsubscribeController::class, 'show'], test()->subscriber->uuid))
        ->assertSee('Are you sure you want to unsubscribe from list')
        ->assertSee('document.getElementById');
});

it('can load the unsubscribe url with a send and wont automatically confirm right away', function () {
    $send = Send::factory()->create([
        'subscriber_id' => test()->subscriber->id,
        'created_at' => now(),
    ]);

    $this
        ->get(action([UnsubscribeController::class, 'show'], [test()->subscriber->uuid, $send->uuid]))
        ->assertSee('Are you sure you want to unsubscribe from list')
        ->assertDontSee('document.getElementById');
});

it('can load the unsubscribe url with a send and will automatically confirm after 5 minutes', function () {
    $send = Send::factory()->create([
        'subscriber_id' => test()->subscriber->id,
        'created_at' => now()->subMinutes(5),
    ]);

    $this
        ->get(action([UnsubscribeController::class, 'show'], [test()->subscriber->uuid, $send->uuid]))
        ->assertSee('Are you sure you want to unsubscribe from list')
        ->assertSee('document.getElementById');
});
