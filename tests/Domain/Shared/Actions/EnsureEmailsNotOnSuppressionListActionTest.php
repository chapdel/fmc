<?php

namespace Spatie\Mailcoach\Tests\Domain\Shared\Actions;

use InvalidArgumentException;
use Spatie\Mailcoach\Domain\Audience\Models\Suppression;
use Spatie\Mailcoach\Domain\Shared\Actions\EnsureEmailsNotOnSuppressionListAction;
use Spatie\Mailcoach\Domain\TransactionalMail\Exceptions\SuppressedEmail;

beforeEach(function () {
    test()->action = resolve(EnsureEmailsNotOnSuppressionListAction::class);
});

it('can check if an email is not on the suppression list', function () {
    $result = test()->action->execute('info@example.com');

    expect($result)->toBeNull();
});

it('can check if an email is on the suppression list', function () {
    $suppression = Suppression::factory()->create();

    test()->action->execute($suppression->email);
})->throws(SuppressedEmail::class);

it('cannot check with an invalid email', function () {
    test()->action->execute('invalid-email');
})->throws(InvalidArgumentException::class);

it('can check if an multiple emails are not on the suppression list', function () {
    $result = test()->action->execute(['info@example.com', 'info2@example.com']);

    expect($result)->toBeNull();
});

it('can check if an email in a list argument is on the suppression list', function () {
    $suppression = Suppression::factory()->create();

    test()->action->execute(['info@example.com', 'info2@example.com', $suppression->email]);
})->throws(SuppressedEmail::class);
