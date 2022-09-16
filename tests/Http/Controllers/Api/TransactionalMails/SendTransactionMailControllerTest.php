<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\TransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail as TransactionalMailModel;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails\SendTransactionalMailController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    TransactionalMailTemplate::factory()->create([
        'name' => 'my-template',
        'body' => 'My template body',
    ]);
});

it('can send a transactional mail', function () {
    Mail::fake();

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'template' => 'my-template',
            'subject' => 'Some subject',
            'from' => 'rias@spatie.be',
            'to' => 'freek@spatie.be',
            'cc' => 'rias+cc@spatie.be',
            'bcc' => 'rias+bcc@spatie.be',
        ]))
        ->assertSuccessful();

    Mail::assertSent(TransactionalMail::class, function (TransactionalMail $mail) {
        expect($mail->subject)->toBe('Some subject');
        expect($mail->from)->toBe([['name' => null, 'address' => 'rias@spatie.be']]);
        expect($mail->to)->toBe([['name' => '', 'address' => 'freek@spatie.be']]);
        expect($mail->cc)->toBe([['name' => '', 'address' => 'rias+cc@spatie.be']]);
        expect($mail->bcc)->toBe([['name' => '', 'address' => 'rias+bcc@spatie.be']]);

        return true;
    });
});

it('tracks the transactional mails', function () {
    $this
        ->post(action(SendTransactionalMailController::class, [
            'template' => 'my-template',
            'subject' => 'Some subject',
            'from' => 'rias@spatie.be',
            'to' => 'freek@spatie.be',
            'cc' => 'rias+cc@spatie.be',
            'bcc' => 'rias+bcc@spatie.be',
            'store' => true,
        ]))
        ->assertSuccessful();

    expect(TransactionalMailModel::count())->toBe(1);
    expect(TransactionalMailModel::first()->body)->toContain('My template body');
});

it('can handle the fields of a transactional mail', function() {
    TransactionalMailTemplate::factory()->create([
        'name' => 'my-template-with-placeholders',
        'body' => 'title: [[[title]]], body: [[[body]]]',
    ]);

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'template' => 'my-template-with-placeholders',
            'subject' => 'Some subject',
            'from' => 'rias@spatie.be',
            'to' => 'freek@spatie.be',
            'fields' => [
                'title' => 'my title',
                'body' => 'my body',
                'unused' => 'some value',
            ],
        ]))
        ->assertSuccessful();

    expect(TransactionalMailModel::first()->body)->toContain('title: my title, body: my body');

});
