<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\TransactionalMail\Events\TransactionalMailLinkClickedEvent;
use Spatie\Mailcoach\Domain\TransactionalMail\Events\TransactionalMailOpenedEvent;
use Spatie\Mailcoach\Domain\TransactionalMail\Events\TransactionalMailStored;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Tests\Domain\TransactionalMail\Concerns\SendsTestTransactionalMail;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestTransactionMail;

uses(TestCase::class);
uses(SendsTestTransactionalMail::class);

test('a transactional mail will be stored in the db', function () {
    test()->sendTestMail(function (TestTransactionMail $mail) {
        $mail
            ->subject('This is the subject')
            ->trackOpensAndClicks();
    });

    test()->assertCount(1, TransactionalMail::get());
    test()->assertCount(1, Send::get());

    $transactionalMail = TransactionalMail::first();

    test()->assertEquals(
        [['email' => config('mail.from.address'), 'name' => config('mail.from.name')]],
        $transactionalMail->from,
    );
    test()->assertEquals('This is the subject', $transactionalMail->subject);
    test()->assertStringContainsString('This is the content for John Doe', $transactionalMail->body);
    test()->assertTrue($transactionalMail->track_opens);
    test()->assertTrue($transactionalMail->track_clicks);
    test()->assertEquals(TestTransactionMail::class, $transactionalMail->mailable_class);
    test()->assertInstanceOf(Send::class, $transactionalMail->send);
    test()->assertInstanceOf(TransactionalMail::class, Send::first()->transactionalMail);
});

it('can store the various recipients', function () {
    test()->sendTestMail(function (TestTransactionMail $testTransactionMail) {
        $testTransactionMail
            ->trackOpensAndClicks()
            ->from('ringo@example.com', 'Ringo')
            ->to('john@example.com', 'John')
            ->cc('paul@example.com', 'Paul')
            ->bcc('george@example.com', 'George');
    });

    $transactionalMail = TransactionalMail::first();

    test()->assertEquals(
        [['email' => 'ringo@example.com', 'name' => 'Ringo']],
        $transactionalMail->from,
    );

    test()->assertEquals(
        [['email' => 'john@example.com', 'name' => 'John']],
        $transactionalMail->to,
    );

    test()->assertEquals(
        [['email' => 'paul@example.com', 'name' => 'Paul']],
        $transactionalMail->cc,
    );

    test()->assertEquals(
        [['email' => 'george@example.com', 'name' => 'George']],
        $transactionalMail->bcc,
    );
});

test('storing a transactional mail dispatches an event', function () {
    Event::fake([TransactionalMailStored::class]);

    test()->sendTestMail(function (TestTransactionMail $mail) {
        $mail->store();
    });

    Event::assertDispatched(TransactionalMailStored::class, function (TransactionalMailStored $event) {
        test()->assertNotNull($event->transactionalMail->id);

        return $event;
    });
});

test('only opens on transactional mails can be tracked', function () {
    test()->sendTestMail(function (TestTransactionMail $mail) {
        $mail->trackOpens();
    });

    $transactionalMail = TransactionalMail::first();
    test()->assertTrue($transactionalMail->track_opens);
    test()->assertFalse($transactionalMail->track_clicks);
});

test('only click on transactional mails can be tracked', function () {
    test()->sendTestMail(function (TestTransactionMail $mail) {
        $mail->store();
    });

    $transactionalMail = TransactionalMail::first();
    test()->assertFalse($transactionalMail->track_opens);
    test()->assertFalse($transactionalMail->track_clicks);
});

test('by default it will not store any mails', function () {
    test()->sendTestMail(function (TestTransactionMail $mail) {
    });

    test()->assertCount(0, TransactionalMail::get());
});

test('a send for a transactional mail can be marked as opened', function () {
    Event::fake([TransactionalMailOpenedEvent::class]);

    test()->sendTestMail();

    /** @var Send $send */
    $send = Send::first();

    $send->registerOpen();

    test()->assertCount(1, $send->transactionalMailOpens);

    Event::assertDispatched(TransactionalMailOpenedEvent::class);
});

test('a send for a transactional mail can be marked as clicked', function () {
    Event::fake([TransactionalMailLinkClickedEvent::class]);

    test()->sendTestMail();

    /** @var Send $send */
    $send = Send::first();

    $send->registerClick('https://spatie.be');

    test()->assertCount(1, $send->transactionalMailClicks);

    Event::assertDispatched(TransactionalMailLinkClickedEvent::class);
});
