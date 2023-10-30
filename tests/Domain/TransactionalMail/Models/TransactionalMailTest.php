<?php

use Spatie\Mailcoach\Tests\Factories\TransactionalMailFactory;

it('can group clicks per url', function () {
    /** @var \Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem $transactionalMail */
    $transactionalMail = TransactionalMailFactory::new()
        ->withClick(['url' => 'https://spatie.be'], 2)
        ->withClick(['url' => 'https://mailcoach.app'], 3)
        ->create();

    expect($transactionalMail->contentItem->clicks()->count())->toBe(5);

    $groupedPerUrl = $transactionalMail->clicksPerUrl();

    expect('https://mailcoach.app')->toEqual($groupedPerUrl[0]['url']);
    expect(3)->toEqual($groupedPerUrl[0]['count']);

    expect('https://spatie.be')->toEqual($groupedPerUrl[1]['url']);
    expect(2)->toEqual($groupedPerUrl[1]['count']);
});
