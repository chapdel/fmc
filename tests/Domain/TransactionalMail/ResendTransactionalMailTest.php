<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\ResendTransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Tests\Domain\TransactionalMail\Concerns\SendsTestTransactionalMail;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestTransactionMail;

uses(TestCase::class);
uses(SendsTestTransactionalMail::class);

it('can resend a transactional mail', function () {
    test()->sendTestMail();

    /** @var TransactionalMail $originalMail */
    test()->sendTestMail(function (TestTransactionMail $testTransactionMail) {
        $testTransactionMail
            ->trackOpensAndClicks()
            ->from('ringo@example.com', 'Ringo')
            ->to('john@example.com', 'John')
            ->cc('paul@example.com', 'Paul')
            ->bcc('george@example.com', 'George');
    });

    Mail::fake();

    $originalMail = TransactionalMail::first();

    $originalMail->resend();

    Mail::assertSent(
        ResendTransactionalMail::class,
        function (ResendTransactionalMail $resentMail) use ($originalMail) {
            expect($originalMail->subject)->toEqual($resentMail->subject);

            assertMatchingPersons($originalMail, $resentMail, 'from');
            assertMatchingPersons($originalMail, $resentMail, 'to');
            assertMatchingPersons($originalMail, $resentMail, 'cc');
            assertMatchingPersons($originalMail, $resentMail, 'bcc');

            return true;
        }
    );

    expect(TransactionalMail::get())->toHaveCount(2);
});

// Helpers
function assertMatchingPersons(TransactionalMail $originalMail, ResendTransactionalMail $resentMail, string $field)
{
    expect(count($resentMail->to))->toBeGreaterThan(0);
    foreach ($originalMail->$field as $person) {
        test()->assertTrue(in_array($person['email'], collect($resentMail->$field)->pluck('address')->toArray()));
    }
}
