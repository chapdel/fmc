<?php

use Spatie\Mailcoach\Tests\Factories\TransactionalMailFactory;

test('the open relation works', function () {
    $transactionalMailWithoutOpen = TransactionalMailFactory::new()->create();

    $transactionalMailWithOpen = TransactionalMailFactory::new()
        ->withOpen()
        ->create();

    expect($transactionalMailWithoutOpen->opens)->toHaveCount(0);
    expect($transactionalMailWithOpen->opens)->toHaveCount(1);
});

test('the click relation works', function () {
    $transactionalMailWithoutClick = TransactionalMailFactory::new()->create();

    $transactionalMailWithClick = TransactionalMailFactory::new()
        ->withClick()
        ->create();

    expect($transactionalMailWithoutClick->clicks)->toHaveCount(0);
    expect($transactionalMailWithClick->clicks)->toHaveCount(1);
});

it('can group clicks per url', function () {
    /** @var \Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail $transactionalMail */
    $transactionalMail = TransactionalMailFactory::new()
        ->withClick(['url' => 'https://spatie.be'], 2)
        ->withClick(['url' => 'https://mailcoach.app'], 3)
        ->create();

    expect($transactionalMail->clicks)->toHaveCount(5);

    $groupedPerUrl = $transactionalMail->clicksPerUrl();

    expect('https://mailcoach.app')->toEqual($groupedPerUrl[0]['url']);
    expect(3)->toEqual($groupedPerUrl[0]['count']);

    expect('https://spatie.be')->toEqual($groupedPerUrl[1]['url']);
    expect(2)->toEqual($groupedPerUrl[1]['count']);
});
