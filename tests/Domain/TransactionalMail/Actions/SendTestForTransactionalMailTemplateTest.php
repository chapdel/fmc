<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\TransactionalMail\Actions\SendTestForTransactionalMailTemplateAction;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Tests\TestClasses\TestMailableWithTemplate;

it('can send a test for a transactional mail template', function () {
    Mail::fake();

    $template = TransactionalMail::factory()->create([
        'name' => 'test-template',
        'test_using_mailable' => TestMailableWithTemplate::class,
    ]);

    $template->contentItem->update([
        'html' => 'test html {{ $argument }}',
    ]);

    (new SendTestForTransactionalMailTemplateAction())->execute(
        ['john@example.com'],
        $template,
    );

    Mail::assertSent(function (TestMailableWithTemplate $mail) {
        expect($mail->hasTo('john@example.com'))->toBeTrue();
        $mail->assertSeeInHtml('test html');

        return true;
    });
});

it('will not use cc or bcc when sending out a test', function () {
    Mail::fake();

    $template = TransactionalMail::factory()->create([
        'name' => 'test-template',
        'cc' => ['jane@example.com'],
        'bcc' => ['tarzan@example.com'],
        'test_using_mailable' => TestMailableWithTemplate::class,
    ]);

    (new SendTestForTransactionalMailTemplateAction())->execute(
        ['john@example.com'],
        $template,
    );

    Mail::assertSent(function (TestMailableWithTemplate $mail) {
        expect($mail->hasCc('jane@example.com'))->toBeFalse();
        expect($mail->hasBcc('tarzan@example.com'))->toBeFalse();

        return true;
    });
});
