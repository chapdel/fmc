<?php

use Spatie\Mailcoach\Domain\Shared\Support\License\License;

it('can determine that there is no license', function () {
    expect((new License())->getStatus())->not->toBeNull();
});
