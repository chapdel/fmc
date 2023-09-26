<?php

use Spatie\Mailcoach\Domain\ConditionBuilder\Actions\CreateConditionFromKeyAction;
use Spatie\Mailcoach\Domain\ConditionBuilder\Conditions\Condition;
use Spatie\Mailcoach\Domain\ConditionBuilder\Exceptions\ConditionException;

use function PHPUnit\Framework\assertInstanceOf;

it('can create a condition from a string', function () {
    $action = new CreateConditionFromKeyAction();

    assertInstanceOf(Condition::class, $action->execute('subscriber_tags'));
});

it('throws an exception for unknown condition keys', function () {
    $action = new CreateConditionFromKeyAction();

    $action->execute('unknown');
})->throws(ConditionException::class, 'Unable to create a condition for key `unknown`.');
