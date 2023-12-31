<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Content\Events\LinkClickedEvent;
use Spatie\Mailcoach\Domain\Content\Models\Link;

it('will fire an event when a link gets clicked', function () {
    Event::fake(LinkClickedEvent::class);

    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send $send */
    $send = SendFactory::new()->create();

    $send->registerClick('https://spatie.be');

    expect(Link::get())->toHaveCount(1);

    test()->assertDatabaseHas('mailcoach_links', [
        'content_item_id' => $send->content_item_id,
        'url' => 'https://spatie.be',
    ]);

    test()->assertDatabaseHas('mailcoach_clicks', [
        'send_id' => $send->id,
        'link_id' => Link::first()->id,
        'subscriber_id' => $send->subscriber->id,
    ]);

    Event::assertDispatched(LinkClickedEvent::class, function (LinkClickedEvent $event) use ($send) {
        expect($event->click->send->uuid)->toEqual($send->uuid);

        return true;
    });
});
