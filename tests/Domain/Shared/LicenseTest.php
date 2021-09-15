<?php

use Spatie\Mailcoach\Domain\Shared\Support\License\License;

it('can determine that there is no license', function () {
    test()->assertNotNull((new License())->getStatus());
});
