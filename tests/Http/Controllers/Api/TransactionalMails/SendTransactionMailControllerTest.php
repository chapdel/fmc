<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Policies\SendPolicy;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\TransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail as TransactionalMailModel;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;
use Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails\SendTransactionalMailController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestClasses\CustomSendDenyAllPolicy;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    TransactionalMailModel::factory()->create([
        'name' => 'my-template',
        'from' => 'john@doe.com',
        'subject' => 'An other subject',
        'body' => 'My template body',
    ]);
});

it('can send a transactional mail', function () {
    Mail::fake();

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template',
            'subject' => 'Some subject',
            'from' => 'rias@spatie.be',
            'to' => 'freek@spatie.be',
            'cc' => 'rias+cc@spatie.be',
            'bcc' => 'rias+bcc@spatie.be',
        ]))
        ->assertSuccessful();

    Mail::assertSent(TransactionalMail::class, function (TransactionalMail $mail) {
        $mail->build();

        expect($mail->subject)->toBe('Some subject');
        expect($mail->from)->toBe([['name' => null, 'address' => 'rias@spatie.be']]);
        expect($mail->to)->toContain(['name' => '', 'address' => 'freek@spatie.be']);
        expect($mail->cc)->toContain(['name' => '', 'address' => 'rias+cc@spatie.be']);
        expect($mail->bcc)->toContain(['name' => '', 'address' => 'rias+bcc@spatie.be']);

        return true;
    });
});

it('tracks the transactional mails', function () {
    $this
        ->post(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template',
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

it('authorizes with policies', function () {
    $this->withExceptionHandling();

    app()->bind(SendPolicy::class, CustomSendDenyAllPolicy::class);

    $this
        ->post(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template',
            'subject' => 'Some subject',
            'from' => 'rias@spatie.be',
            'to' => 'freek@spatie.be',
            'cc' => 'rias+cc@spatie.be',
            'bcc' => 'rias+bcc@spatie.be',
            'store' => true,
        ]))
        ->assertForbidden();
});

it('will not store mail when asked not to store mails', function () {
    $this
        ->post(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template',
            'subject' => 'Some subject',
            'from' => 'rias@spatie.be',
            'to' => 'freek@spatie.be',
            'store' => false,
        ]))
        ->assertSuccessful();

    expect(TransactionalMailLogItem::count())->toBe(0);
});

it('can handle the fields of a transactional mail', function () {
    $template = Template::factory()->create([
        'html' => '<html>title: [[[title]]], body: [[[body]]]</html>',
    ]);

    /** TransactionalMail */
    TransactionalMailModel::factory()->create([
        'template_id' => $template->id,
        'name' => 'my-template-with-placeholders',
        'body' => '<html>title: ::myTitle::</html>',
    ]);

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template-with-placeholders',
            'subject' => 'Some subject',
            'from' => 'rias@spatie.be',
            'to' => 'freek@spatie.be',
            'replacements' => [
                'myTitle' => 'replaced title',
            ],
        ]))
        ->assertSuccessful();

    expect(TransactionalMailLogItem::first()->body)
        ->toContain('title: replaced title');
});
