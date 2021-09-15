<?php

use Spatie\Mailcoach\Domain\Audience\Actions\Subscribers\UpdateSubscriberAction;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Support\Config;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    test()->subscriber = Subscriber::factory()->create();

    test()->emailList = EmailList::factory()->create();

    test()->anotherEmailList = EmailList::factory()->create();

    test()->newAttributes = [
        'email' => 'john@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ];
});

it('can update the attributes of a subscriber', function () {
    $updateSubscriberAction = Config::getAutomationActionClass('update_subscriber', UpdateSubscriberAction::class);

    $updateSubscriberAction->execute(
        test()->subscriber,
        test()->newAttributes,
    );

    test()->subscriber->refresh();

    test()->assertEquals('john@example.com', test()->subscriber->email);
    test()->assertEquals('John', test()->subscriber->first_name);
    test()->assertEquals('Doe', test()->subscriber->last_name);
});
