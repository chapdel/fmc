<?php

use Spatie\Mailcoach\Domain\Vendor\Sendgrid\Actions\AddUniqueArgumentsMailHeader;

test('the listener does not contain syntax errors', function () {
    new AddUniqueArgumentsMailHeader();

    expect(true)->toBeTrue();
});
