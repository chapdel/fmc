<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Subscribers\DestroySubscriberController;

it('can delete a subscriber', function () {
    test()->authenticate();

    $subscriber = Subscriber::factory()->create();

    $this
        ->delete(action(DestroySubscriberController::class, [$subscriber->emailList->id, $subscriber->id]))
        ->assertRedirect();

    expect(Subscriber::get())->toHaveCount(0);
});
