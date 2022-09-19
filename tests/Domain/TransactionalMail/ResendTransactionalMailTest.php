<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\ResendTransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;
use Spatie\Mailcoach\Tests\Domain\TransactionalMail\Concerns\SendsTestTransactionalMail;
use Spatie\Mailcoach\Tests\TestClasses\TestTransactionMail;

uses(SendsTestTransactionalMail::class);

it('can resend a transactional mail', function () {
    $this->sendTestMail();

    /** @var TransactionalMailLogItem $originalMail */
    $this->sendTestMail(function (TestTransactionMail $testTransactionMail) {
        $testTransactionMail
            ->store()
            ->from('ringo@example.com', 'Ringo')
            ->to('john@example.com', 'John')
            ->cc('paul@example.com', 'Paul')
            ->bcc('george@example.com', 'George');
    });

    Mail::fake();

    $originalMail = TransactionalMailLogItem::first();

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

    expect(TransactionalMailLogItem::get())->toHaveCount(2);
});

// Helpers
function assertMatchingPersons(TransactionalMailLogItem $originalMail, ResendTransactionalMail $resentMail, string $field)
{
    expect(count($resentMail->to))->toBeGreaterThan(0);
    foreach ($originalMail->$field as $person) {
        test()->assertTrue(in_array($person['email'], collect($resentMail->$field)->pluck('address')->toArray()));
    }
}
