<?php

namespace Spatie\Mailcoach\Tests\Domain\Audience\Models;

use Spatie\Mailcoach\Domain\Audience\Enums\SuppressionOrigin;
use Spatie\Mailcoach\Domain\Audience\Enums\SuppressionReason;
use Spatie\Mailcoach\Domain\Audience\Models\Suppression;

it('can create models', function () {
    $suppression = Suppression::fromClient('spam@example.com', SuppressionReason::hardBounce);

    expect($suppression->origin)->toBe(SuppressionOrigin::Client);
    expect($suppression->email)->toBe('spam@example.com');
});

it('can create a model by an admin', function () {
    $suppression = Suppression::fromAdmin('spam@example.com');

    expect($suppression->origin)->toBe(SuppressionOrigin::Admin);
    expect($suppression->reason)->toBe(SuppressionReason::manual);
    expect($suppression->email)->toBe('spam@example.com');
});
