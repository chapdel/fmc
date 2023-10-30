<?php

use Spatie\Mailcoach\Domain\Shared\Actions\CommaSeparatedEmailsToArrayAction;

it('can convert a comma separated list to an array', function (string $emails, ?string $names, $expectedResult) {
    $action = resolve(CommaSeparatedEmailsToArrayAction::class);

    $actualResult = $action->execute($emails, $names);

    expect($actualResult)->toBe($expectedResult);
})->with('success');

// Datasets
dataset('success', function () {
    // Single values
    yield ['example@spatie.be', 'example', [['email' => 'example@spatie.be', 'name' => 'example']]];
    yield ['example@spatie.be', '', [['email' => 'example@spatie.be', 'name' => null]]];
    yield ['example@spatie.be', null, [['email' => 'example@spatie.be', 'name' => null]]];

    // Multiple values
    yield ['example@spatie.be,example2@spatie.be', 'example,example2', [['email' => 'example@spatie.be', 'name' => 'example'], ['email' => 'example2@spatie.be', 'name' => 'example2']]];
    yield ['example@spatie.be,example2@spatie.be', 'example', [['email' => 'example@spatie.be', 'name' => 'example'], ['email' => 'example2@spatie.be', 'name' => null]]];

    // Verify trim() functionality
    yield ['example@spatie.be, example2@spatie.be', 'example,example2', [['email' => 'example@spatie.be', 'name' => 'example'], ['email' => 'example2@spatie.be', 'name' => 'example2']]];
    yield ['example@spatie.be,example2@spatie.be', 'example, example2', [['email' => 'example@spatie.be', 'name' => 'example'], ['email' => 'example2@spatie.be', 'name' => 'example2']]];
});
