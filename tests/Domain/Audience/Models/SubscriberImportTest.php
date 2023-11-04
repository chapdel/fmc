<?php

use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;

it('can handle the old format of errors in errorCount', function () {
    $subscriberImport = SubscriberImport::factory()->create([
        'errors' => json_encode([
            ['email' => 'n', 'message' => 'Does not have a valid email'],
        ]),
    ]);

    expect($subscriberImport->errorCount())->toBe(1);
});
