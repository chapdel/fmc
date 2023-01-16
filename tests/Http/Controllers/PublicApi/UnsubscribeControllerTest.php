<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
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
        ->assertSee('Are you sure you want to unsubscribe from list');
});
