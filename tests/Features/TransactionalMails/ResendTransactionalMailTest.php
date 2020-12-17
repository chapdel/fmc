<?php

namespace Spatie\Mailcoach\Tests\Features\TransactionalMails;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\ResendTransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Tests\Features\TransactionalMails\Concerns\SendsTestTransactionalMail;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestTransactionMail;

class ResendTransactionalMailTest extends TestCase
{
    use SendsTestTransactionalMail;

    /** @test */
    public function it_can_resend_a_transactional_mail()
    {
        $this->sendTestMail();

        /** @var TransactionalMail $originalMail */
        $this->sendTestMail(function(TestTransactionMail $testTransactionMail) {
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
                $this->assertEquals($resentMail->subject, $originalMail->subject);

                $this->assertMatchingPersons($originalMail, $resentMail, 'from');
                $this->assertMatchingPersons($originalMail, $resentMail, 'to');
                $this->assertMatchingPersons($originalMail, $resentMail, 'cc');
                $this->assertMatchingPersons($originalMail, $resentMail, 'bcc');

                return true;
            });

        $this->assertCount(2, TransactionalMail::get());
    }

    protected function assertMatchingPersons(TransactionalMail $originalMail, ResendTransactionalMail $resentMail, string $field)
    {
        $this->assertGreaterThan(0, count($resentMail->to));
        foreach($originalMail->$field as $person) {
            $this->assertTrue(in_array($person['email'], collect($resentMail->$field)->pluck('address')->toArray()));
        }
    }
}
