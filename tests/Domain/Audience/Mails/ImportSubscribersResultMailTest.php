<?php

use Spatie\Mailcoach\Domain\Audience\Mails\ImportSubscribersResultMail;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('can render the import subscribers result mail', function () {
    $subscriberImport = SubscriberImport::factory()->create();

    test()->assertIsString((new ImportSubscribersResultMail($subscriberImport))->render());
});
