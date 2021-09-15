<?php

use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails\TransactionalMailsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);
uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    TransactionalMail::factory()->count(2)->create(['subject' => 'foo']);
    TransactionalMail::factory()->count(2)->create(['subject' => 'bar']);
});

it('can show all transactional mails', function () {
    $transactionalMails = $this
        ->get(action(TransactionalMailsController::class))
        ->assertSuccessful()
        ->json('data');

    test()->assertCount(4, $transactionalMails);
});

it('can search mails with a certain subject', function () {
    $transactionalMails = $this
                ->get(action(TransactionalMailsController::class). '?filter[search]=ba')
                ->assertSuccessful()
                ->json('data');

    test()->assertCount(2, $transactionalMails);

    test()->assertEquals('bar', $transactionalMails[0]['subject']);
});
