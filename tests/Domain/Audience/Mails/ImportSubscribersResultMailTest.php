<?php

use Spatie\Mailcoach\Domain\Audience\Mails\ImportSubscribersResultMail;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;

it('can render the import subscribers result mail', function () {
    $subscriberImport = SubscriberImport::factory()->create();

    expect((new ImportSubscribersResultMail($subscriberImport))->render())->toBeString();
});
