<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Content\Events\ContentOpenedEvent;
use Spatie\Mailcoach\Domain\Content\Events\LinkClickedEvent;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Domain\TransactionalMail\Events\TransactionalMailStored;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;
use Spatie\Mailcoach\Tests\Domain\TransactionalMail\Concerns\SendsTestTransactionalMail;
use Spatie\Mailcoach\Tests\TestClasses\TestTransactionEnvelopeStyleMail;
use Spatie\Mailcoach\Tests\TestClasses\TestTransactionMail;

uses(SendsTestTransactionalMail::class);

test('a transactional mail will be stored in the db', function () {
    test()->sendTestMail(function (TestTransactionMail $mail) {
        $mail
            ->subject('This is the subject')
            ->attachData('some-content', 'example.pdf')
            ->store();
    });

    expect(TransactionalMailLogItem::get())->toHaveCount(1);
    expect(Send::get())->toHaveCount(1);

    $transactionalMail = TransactionalMailLogItem::first();

    expect($transactionalMail->from)->toEqual([['email' => config('mail.from.address'), 'name' => config('mail.from.name')]]);
    expect($transactionalMail->contentItem->subject)->toEqual('This is the subject');
    expect($transactionalMail->contentItem->html)->toContain('This is the content for John Doe');
    expect($transactionalMail->attachments)->toContain('example.pdf');
    expect($transactionalMail->mailable_class)->toEqual(TestTransactionMail::class);
    expect($transactionalMail->send)->toBeInstanceOf(Send::class);
    expect(Send::first())->contentItem->model->toBeInstanceOf(TransactionalMailLogItem::class);
});

it('can store a mailable that uses envelope and content methods', function () {
    Mail::to('john@example.com')->send(new TestTransactionEnvelopeStyleMail());

    $transactionalMail = TransactionalMailLogItem::first();

    expect($transactionalMail->contentItem->subject)->toEqual('Test mail envelope style');
});

it('can store the various recipients', function () {
    test()->sendTestMail(function (TestTransactionMail $testTransactionMail) {
        $testTransactionMail
            ->store()
            ->from('ringo@example.com', 'Ringo')
            ->cc('paul@example.com', 'Paul')
            ->bcc('george@example.com', 'George');
    });

    $transactionalMail = TransactionalMailLogItem::first();

    expect($transactionalMail->from)->toEqual([['email' => 'ringo@example.com', 'name' => 'Ringo']]);

    expect($transactionalMail->to)->toEqual([['email' => 'john@example.com', 'name' => '']]);

    expect($transactionalMail->cc)->toEqual([['email' => 'paul@example.com', 'name' => 'Paul']]);

    expect($transactionalMail->bcc)->toEqual([['email' => 'george@example.com', 'name' => 'George']]);
});

test('storing a transactional mail dispatches an event', function () {
    Event::fake([TransactionalMailStored::class]);

    test()->sendTestMail(function (TestTransactionMail $mail) {
        $mail->store();
    });

    Event::assertDispatched(TransactionalMailStored::class, function (TransactionalMailStored $event) {
        expect($event->transactionalMail->id)->not->toBeNull();

        return $event;
    });
});

test('by default it will not store any mails', function () {
    test()->sendTestMail(function (TestTransactionMail $mail) {
    });

    expect(TransactionalMailLogItem::get())->toHaveCount(0);
});

test('a send for a transactional mail can be marked as opened', function () {
    Event::fake([ContentOpenedEvent::class]);

    test()->sendTestMail();

    /** @var Send $send */
    $send = Send::first();

    $send->registerOpen();

    expect($send->opens)->toHaveCount(1);

    Event::assertDispatched(ContentOpenedEvent::class);
});

test('a send for a transactional mail can be marked as clicked', function () {
    Event::fake([LinkClickedEvent::class]);

    test()->sendTestMail();

    /** @var Send $send */
    $send = Send::first();

    $send->registerClick('https://spatie.be');

    expect($send->clicks)->toHaveCount(1);

    Event::assertDispatched(LinkClickedEvent::class);
});
