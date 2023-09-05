<?php

namespace Spatie\Mailcoach\Tests\Domain\Shared\Actions;

use InvalidArgumentException;
use Spatie\Mailcoach\Domain\Audience\Models\Suppression;
use Spatie\Mailcoach\Domain\Shared\Actions\IsEmailOnSuppressionListAction;

beforeEach(function () {
    test()->action = resolve(IsEmailOnSuppressionListAction::class);
});

it('can check if an email is not on the suppression list', function () {
    $result = test()->action->execute('info@example.com');

    expect($result)->toBeFalse();
});

it('can check if an email is on the suppression list', function () {
    $suppression = Suppression::factory()->create();

    $result = test()->action->execute($suppression->email);

    expect($result)->toBeTrue();
});

it('cannot check with an invalid email', function () {
    test()->action->execute('invalid-email');
})->throws(InvalidArgumentException::class);
