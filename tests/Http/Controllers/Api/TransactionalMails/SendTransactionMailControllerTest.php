<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Models\Suppression;
use Spatie\Mailcoach\Domain\Shared\Policies\SendPolicy;
use Spatie\Mailcoach\Domain\Template\Models\Template;
use Spatie\Mailcoach\Domain\TransactionalMail\Mails\TransactionalMail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail as TransactionalMailModel;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailLogItem;
use Spatie\Mailcoach\Http\Api\Controllers\TransactionalMails\SendTransactionalMailController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestClasses\CustomSendDenyAllPolicy;
use Symfony\Component\Mime\Email;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    $transactionalMail = TransactionalMailModel::factory()->create([
        'name' => 'my-template',
        'from' => 'john@doe.com',
    ]);

    $transactionalMail->contentItem->update([
        'subject' => 'An other subject',
        'html' => 'My template body',
    ]);
});

it('can send a transactional mail', function () {
    Mail::fake();

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template',
            'from' => 'rias@spatie.be',
            'to' => 'freek@spatie.be',
            'cc' => 'rias+cc@spatie.be',
            'bcc' => 'rias+bcc@spatie.be',
        ]))
        ->assertSuccessful();

    Mail::assertSent(TransactionalMail::class, function (TransactionalMail $mail) {
        $mail->build();

        expect($mail->from)->toBe([['name' => '', 'address' => 'rias@spatie.be']]);
        expect($mail->to)->toContain(['name' => '', 'address' => 'freek@spatie.be']);
        expect($mail->cc)->toContain(['name' => '', 'address' => 'rias+cc@spatie.be']);
        expect($mail->bcc)->toContain(['name' => '', 'address' => 'rias+bcc@spatie.be']);

        return true;
    });
});

test('not everything is required', function () {
    Mail::fake();

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template',
            'from' => 'rias@spatie.be',
            'to' => 'freek@spatie.be',
            'cc' => null,
            'bcc' => '',
        ]))
        ->assertSuccessful();

    Mail::assertSent(TransactionalMail::class, function (TransactionalMail $mail) {
        $mail->build();

        expect($mail->from)->toBe([['name' => '', 'address' => 'rias@spatie.be']]);
        expect($mail->to)->toContain(['name' => '', 'address' => 'freek@spatie.be']);

        return true;
    });
});

test('it can override subject', function () {
    Mail::fake();

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template',
            'from' => 'rias@spatie.be',
            'to' => 'freek@spatie.be',
            'subject' => 'My custom subject',
        ]))
        ->assertSuccessful();

    Mail::assertSent(TransactionalMail::class, function (TransactionalMail $mail) {
        $mail->build();

        expect($mail->subject)->toBe('My custom subject');

        return true;
    });
});

test('it takes template subject when nothing is passed', function () {
    Mail::fake();

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template',
            'from' => 'rias@spatie.be',
            'to' => 'freek@spatie.be',
        ]))
        ->assertSuccessful();

    Mail::assertSent(TransactionalMail::class, function (TransactionalMail $mail) {
        $mail->build();

        expect($mail->subject)->toBe('An other subject');

        return true;
    });
});

it('can send a transactional mail to formatted emails', function () {
    Mail::fake();

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template',
            'from' => 'rias@spatie.be',
            'to' => '"Freek" <freek@spatie.be>',
            'cc' => '"Rias" <rias+cc@spatie.be>',
            'bcc' => '"Rias" <rias+bcc@spatie.be>',
            'reply_to' => '"Rias" <rias+replyto@spatie.be>',
        ]))
        ->assertSuccessful();

    Mail::assertSent(TransactionalMail::class, function (TransactionalMail $mail) {
        $mail->build();

        expect($mail->from)->toBe([['name' => '', 'address' => 'rias@spatie.be']]);
        expect($mail->to)->toContain(['name' => 'Freek', 'address' => 'freek@spatie.be']);
        expect($mail->cc)->toContain(['name' => 'Rias', 'address' => 'rias+cc@spatie.be']);
        expect($mail->bcc)->toContain(['name' => 'Rias', 'address' => 'rias+bcc@spatie.be']);
        expect($mail->replyTo)->toContain(['name' => 'Rias', 'address' => 'rias+replyto@spatie.be']);

        return true;
    });
});

it('validates email addresses', function () {
    Mail::fake();
    $this->withExceptionHandling();

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template',
            'from' => 'rias@spatie.be',
            'to' => '"Freek" <not-anemail>',
            'cc' => '"Rias" <rias+cc@spatie.be>',
            'bcc' => '"Rias" <rias+bcc@spatie.be>',
        ]))
        ->assertJsonValidationErrorFor('to');
});

it('validates email addresses without tld', function () {
    Mail::fake();
    $this->withExceptionHandling();

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template',
            'from' => 'rias@spatie.be',
            'to' => 'rias@gmail',
            'cc' => '"Rias" <rias+cc@spatie.be>',
            'bcc' => '"Rias" <rias+bcc@spatie.be>',
        ]))
        ->assertJsonValidationErrorFor('to');

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template',
            'from' => 'rias@spatie.be',
            'to' => '"Rias" <rias@gmail>',
            'cc' => '"Rias" <rias+cc@spatie.be>',
            'bcc' => '"Rias" <rias+bcc@spatie.be>',
        ]))
        ->assertJsonValidationErrorFor('to');
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
    expect(TransactionalMailModel::first()->contentItem->html)->toContain('My template body');
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
        'name' => 'my-template-with-placeholders',
    ])->contentItem->update([
        'template_id' => $template->id,
        'subject' => '{{ greeting }}',
        'html' => '<html>title: ::myTitle::</html>',
    ]);

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template-with-placeholders',
            'from' => 'rias@spatie.be',
            'to' => 'freek@spatie.be',
            'replacements' => [
                'myTitle' => 'replaced title',
                'greeting' => 'Hi!',
            ],
        ]))
        ->assertSuccessful();

    expect(TransactionalMailLogItem::first()->contentItem->html)
        ->toContain('title: replaced title');
    expect(TransactionalMailLogItem::first()->contentItem->subject)
        ->toBe('Hi!');
});

it('can render twig', function () {
    $template = Template::factory()->create([
        'html' => '<html>title: [[[title]]], body: [[[body]]]</html>',
    ]);

    /** TransactionalMail */
    TransactionalMailModel::factory()->create([
        'type' => 'html',
        'name' => 'my-template-with-placeholders',
    ])->contentItem->update([
        'template_id' => $template->id,
        'html' => '<html>title: {{ myTitle }}</html>',
        'subject' => '{{ greeting }}',
    ]);

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template-with-placeholders',
            'from' => 'rias@spatie.be',
            'to' => 'freek@spatie.be',
            'replacements' => [
                'myTitle' => 'replaced title',
                'greeting' => 'Hi!',
            ],
        ]))
        ->assertSuccessful();

    expect(TransactionalMailLogItem::first()->contentItem->html)
        ->toContain('title: replaced title');
    expect(TransactionalMailLogItem::first()->contentItem->subject)
        ->toBe('Hi!');
});

it('can render twig inside the template', function () {
    /** TransactionalMail */
    TransactionalMailModel::factory()->create([
        'type' => 'html',
        'name' => 'my-template-with-placeholders',
    ])->contentItem->update([
        'html' => '<html>{% if myTitle %} {{ myTitle }} {% endif %}</html>',
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

    expect(TransactionalMailLogItem::first()->contentItem->html)
        ->toContain('replaced title');
});

it('can render twig inside the template with if tags', function () {
    /** TransactionalMail */
    TransactionalMailModel::factory()->create([
        'type' => 'html',
        'name' => 'my-template-with-placeholders',
    ])->contentItem->update([
        'html' => '<html>{% if myTitle %} {{ myTitle }} {% endif %}</html>',
    ]);

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template-with-placeholders',
            'subject' => 'Some subject',
            'from' => 'rias@spatie.be',
            'to' => 'freek@spatie.be',
            'replacements' => [
            ],
        ]))
        ->assertSuccessful();

    expect(TransactionalMailLogItem::first()->contentItem->html)
        ->toContain("<html></html>\n");
});

it('can accept attachments', function () {
    $this->originalMailer = Mail::getFacadeRoot();

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template',
            'mailer' => 'array',
            'subject' => 'Some subject',
            'from' => 'rias@spatie.be',
            'to' => 'freek@spatie.be',
            'cc' => 'rias+cc@spatie.be',
            'bcc' => 'rias+bcc@spatie.be',
            'attachments' => [
                ['content' => '1234', 'name' => 'embedded.jpg', 'content_type' => 'image/jpg', 'content_id' => 'cid:embedded.jpg'],
                ['content' => '1234', 'name' => 'file.txt', 'content_type' => 'text/plain'],
            ],
        ]))
        ->assertSuccessful();

    $message = getSentMessage();

    expect($message->getAttachments())->not()->toBeEmpty();
    expect($message->getAttachments()[0]->getBody())->toBe(base64_decode('1234'));
    expect($message->getAttachments()[0]->getName())->toBe('embedded.jpg');
    expect($message->getAttachments()[0]->getContentType())->toBe('image/jpg');

    expect($message->getAttachments()[1]->getBody())->toBe(base64_decode('1234'));
    expect($message->getAttachments()[1]->getName())->toBe('file.txt');
    expect($message->getAttachments()[1]->getContentType())->toBe('text/plain');
});

it('can send a mail without a template', function () {
    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'html' => '<html><body>my html</body></html>',
            'mailer' => 'array',
            'subject' => 'Some subject',
            'from' => 'rias@spatie.be',
            'to' => 'freek@spatie.be',
        ]))
        ->assertSuccessful();

    $message = getSentMessage();

    expect($message->getBody()->bodyToString())->toBe('<html><body>my html</body></html>');
});

it('will add html tags when not present on the input', function () {
    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'html' => 'my html',
            'mailer' => 'array',
            'subject' => 'Some subject',
            'from' => 'rias@spatie.be',
            'to' => 'freek@spatie.be',
        ]))
        ->assertSuccessful();

    $message = getSentMessage();

    expect($message->getBody()->bodyToString())->toBe('<html><body>my html</body></html>');
});

it('will not actually sent a mail when a fake parameter is passed', function () {
    Mail::fake();

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'subject' => 'Some subject',
            'from' => 'rias@spatie.be',
            'to' => 'freek@spatie.be',
            'cc' => 'rias+cc@spatie.be',
            'bcc' => 'rias+bcc@spatie.be',
            'html' => 'this is the html',
            'fake' => true,
        ]))->assertSuccessful();

    Mail::assertNothingSent();

    expect(TransactionalMailLogItem::count())->toBe(1);

    /** @var TransactionalMailLogItem $transactionMailLogItem */
    $transactionMailLogItem = TransactionalMailLogItem::first();

    expect($transactionMailLogItem->fake)->toBeTrue();
    expect($transactionMailLogItem->from)->toBe([['name' => '', 'email' => 'rias@spatie.be']]);
    expect($transactionMailLogItem->to)->toBe([['name' => '', 'email' => 'freek@spatie.be']]);
    expect($transactionMailLogItem->cc)->toBe([['name' => '', 'email' => 'rias+cc@spatie.be']]);
    expect($transactionMailLogItem->bcc)->toBe([['name' => '', 'email' => 'rias+bcc@spatie.be']]);
    expect($transactionMailLogItem->contentItem->subject)->toBe('Some subject');
    expect($transactionMailLogItem->contentItem->html)->toBe('<html><body>this is the html</body></html>');
});

it('will not sent when the email is listed as suppressed', function () {
    Mail::fake();

    $suppressed = Suppression::factory()->create(['email' => 'niels@spatie.be']);

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template',
            'from' => 'rias@spatie.be',
            'to' => 'niels@spatie.be',
            'cc' => 'rias+cc@spatie.be',
            'bcc' => 'rias+bcc@spatie.be',
        ]))
        ->assertStatus(406)
        ->assertContent('The email `niels@spatie.be` is on the suppression list.');

    Mail::assertNothingSent();
});

it('validates replacement format', function () {
    Mail::fake();
    $this->withExceptionHandling();

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template',
            'from' => 'rias@spatie.be',
            'to' => 'rias@spatie.be',
            'replacements' => [
                'foo' => ['some', 'array'],
            ],
        ]))
        ->assertStatus(422)
        ->assertJsonValidationErrorFor('replacements.foo');
});

it('replacements can contain an int', function () {
    Mail::fake();
    $this->withExceptionHandling();

    $this
        ->postJson(action(SendTransactionalMailController::class, [
            'mail_name' => 'my-template',
            'from' => 'rias@spatie.be',
            'to' => 'rias@spatie.be',
            'replacements' => [
                'foo' => 1,
            ],
        ]))
        ->assertStatus(204);
});

function getSentMessage(): ?Email
{
    /** @var \Symfony\Component\Mailer\SentMessage $email */
    $email = Mail::mailer('array')->getSymfonyTransport()->messages()->first();

    return $email->getOriginalMessage();
}
