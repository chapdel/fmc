<?php

use Spatie\Mailcoach\Domain\Shared\Support\License\License;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('can determine that there is no license', function () {
    test()->assertNotNull((new License())->getStatus());
});
