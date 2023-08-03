<?php

use Spatie\Mailcoach\Domain\Audience\Mails\ImportSubscribersResultMail;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;

it('can render the import subscribers result mail', function () {
    $subscriberImport = SubscriberImport::factory()->create();

    expect((new ImportSubscribersResultMail($subscriberImport))->render())->toBeString();
});

it('contains a link to the email list', function () {
    $subscriberImport = SubscriberImport::factory()->create();

    expect((new ImportSubscribersResultMail($subscriberImport))->render())->toContain(route('mailcoach.emailLists.import-subscribers', $subscriberImport->emailList));
});

it('does not contain error message if there are no errors', function () {
    $subscriberImport = SubscriberImport::factory()->create([
        'errors' => null,
    ]);

    expect((new ImportSubscribersResultMail($subscriberImport))->render())
        ->not()
        ->toContain('error');
});

it('contains error count', function () {
    $subscriberImport = SubscriberImport::factory()->create([
        'errors' => ['one', 'two'],
    ]);

    expect((new ImportSubscribersResultMail($subscriberImport))->render())
        ->toContain('There were 2 errors.');
});
