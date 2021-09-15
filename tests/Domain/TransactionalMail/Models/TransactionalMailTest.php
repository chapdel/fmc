<?php

use Spatie\Mailcoach\Tests\Factories\TransactionalMailFactory;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

test('the open relation works', function () {
    $transactionalMailWithoutOpen = TransactionalMailFactory::new()->create();

    $transactionalMailWithOpen = TransactionalMailFactory::new()
        ->withOpen()
        ->create();

    test()->assertCount(0, $transactionalMailWithoutOpen->opens);
    test()->assertCount(1, $transactionalMailWithOpen->opens);
});

test('the click relation works', function () {
    $transactionalMailWithoutClick = TransactionalMailFactory::new()->create();

    $transactionalMailWithClick = TransactionalMailFactory::new()
        ->withClick()
        ->create();

    test()->assertCount(0, $transactionalMailWithoutClick->clicks);
    test()->assertCount(1, $transactionalMailWithClick->clicks);
});

it('can group clicks per url', function () {
    /** @var \Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail $transactionalMail */
    $transactionalMail = TransactionalMailFactory::new()
        ->withClick(['url' => 'https://spatie.be'], 2)
        ->withClick(['url' => 'https://mailcoach.app'], 3)
        ->create();

    test()->assertCount(5, $transactionalMail->clicks);

    $groupedPerUrl = $transactionalMail->clicksPerUrl();

    test()->assertEquals($groupedPerUrl[0]['url'], 'https://mailcoach.app');
    test()->assertEquals($groupedPerUrl[0]['count'], 3);

    test()->assertEquals($groupedPerUrl[1]['url'], 'https://spatie.be');
    test()->assertEquals($groupedPerUrl[1]['count'], 2);
});
