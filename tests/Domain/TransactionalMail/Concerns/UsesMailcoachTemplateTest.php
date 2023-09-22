<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;
use Spatie\Mailcoach\Tests\TestClasses\TestMailableWithTemplate;
use Spatie\Mailcoach\Tests\TestClasses\TestTransactionalMailReplacer;

it('can render the template containing blade variables', function () {
    /** @var TransactionalMail $template */
    $template = TransactionalMail::factory()->create([
        'name' => 'test-template',
        'test_using_mailable' => TestMailableWithTemplate::class,
        'type' => 'blade',
    ]);

    $template->contentItem()->update([
        'html' => 'test html {{ $argument }}',
    ]);

    $mailable = $template->getMailable();

    expect($mailable->render())->toContain('test html test-argument');
});

it('can render a template containing markdown and blade variables', function () {
    /** @var TransactionalMail $template */
    $template = TransactionalMail::factory()->create([
        'name' => 'test-template',
        'test_using_mailable' => TestMailableWithTemplate::class,
        'type' => 'blade-markdown',
    ]);

    $template->contentItem()->update([
        'html' => file_get_contents(__DIR__.'/stubs/blade-markdown.blade.php'),
    ]);

    $mailable = $template->getMailable();

    $mailable
        ->assertSeeInHtml('Title</h1>', escape: false)
        ->assertSeeInHtml('Hi all!');
});

it('will not compile blade if it is not allowed', function () {
    /** @var TransactionalMail $template */
    $template = TransactionalMail::factory()->create([
        'name' => 'test-template',
        'test_using_mailable' => TestMailableWithTemplate::class,
        'type' => 'html',
    ]);

    $template->contentItem()->update([
        'html' => 'test html {{ $argument }}',
    ]);

    $mailable = $template->getMailable();

    expect($mailable->render())->toContain('test html {{ $argument }}');
});

it('will use cc and bcc when sending out a mail using the template', function () {
    Mail::fake();

    TransactionalMail::factory()->has(ContentItem::factory())->create([
        'name' => 'test-template',
        'cc' => ['jane@example.com'],
        'bcc' => ['tarzan@example.com'],
        'test_using_mailable' => TestMailableWithTemplate::class,
    ]);

    $mailable = new TestMailableWithTemplate();

    Mail::to('john@example.com')->send($mailable);

    Mail::assertSent(function (TestMailableWithTemplate $mail) {
        expect($mail->hasCc('jane@example.com'))->toBeTrue();
        expect($mail->hasBcc('tarzan@example.com'))->toBeTrue();

        return true;
    });
});

it('will can use replacers to replace content', function () {
    /** @var TransactionalMail $template */
    $template = TransactionalMail::factory()->create([
        'name' => 'test-template',
        'test_using_mailable' => TestMailableWithTemplate::class,
        'replacers' => ['test'],
    ]);

    $template->contentItem()->update([
        'html' => 'test html ::argument::',
    ]);

    config()->set('mailcoach.transactional.replacers', [
        'test' => TestTransactionalMailReplacer::class,
    ]);

    $mailable = $template->getMailable();

    expect($mailable->render())->toContain('test html test-argument-from-replacer');
});

it('will can use replacers to replace subject', function () {
    /** @var TransactionalMail $template */
    $template = TransactionalMail::factory()->create([
        'name' => 'test-template',
        'test_using_mailable' => TestMailableWithTemplate::class,
        'replacers' => ['test'],
    ]);

    $template->contentItem()->update([
        'subject' => '::argument::',
        'html' => '',
    ]);

    config()->set('mailcoach.transactional.replacers', [
        'test' => TestTransactionalMailReplacer::class,
    ]);

    $mailable = $template->getMailable();

    expect($mailable->subject)->toContain('test-argument-from-replacer');
});
