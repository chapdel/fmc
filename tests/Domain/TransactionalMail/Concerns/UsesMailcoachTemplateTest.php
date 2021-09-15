<?php


use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestMailableWithTemplate;
use Spatie\Mailcoach\Tests\TestClasses\TestTransactionalMailReplacer;
use Spatie\Snapshots\MatchesSnapshots;

uses(TestCase::class);
uses(MatchesSnapshots::class);

it('can render the template containing blade variables', function () {
    /** @var TransactionalMailTemplate $template */
    $template = TransactionalMailTemplate::factory()->create([
        'name' => 'test-template',
        'body' => 'test html {{ $argument }}',
        'test_using_mailable' => TestMailableWithTemplate::class,
        'type' => 'blade',
    ]);

    $mailable = $template->getMailable();

    test()->assertStringContainsString('test html test-argument', $mailable->render());
});

it('can render a template containing markdown and blade variables', function () {
    /** @var TransactionalMailTemplate $template */
    $template = TransactionalMailTemplate::factory()->create([
        'name' => 'test-template',
        'body' => file_get_contents(__DIR__ . '/stubs/blade-markdown.blade.php'),
        'test_using_mailable' => TestMailableWithTemplate::class,
        'type' => 'blade-markdown',
    ]);

    $mailable = $template->getMailable();

    test()->assertMatchesHtmlSnapshotWithoutWhitespace($mailable->render());
});

it('will not compile blade if it is not allowed', function () {
    /** @var TransactionalMailTemplate $template */
    $template = TransactionalMailTemplate::factory()->create([
        'name' => 'test-template',
        'body' => 'test html {{ $argument }}',
        'test_using_mailable' => TestMailableWithTemplate::class,
        'type' => 'html',
    ]);

    $mailable = $template->getMailable();

    test()->assertStringContainsString('test html {{ $argument }}', $mailable->render());
});

it('will use cc and bcc when sending out a mail using the template', function () {
    Mail::fake();

    TransactionalMailTemplate::factory()->create([
        'name' => 'test-template',
        'cc' => ['jane@example.com'],
        'bcc' => ['tarzan@example.com'],
        'test_using_mailable' => TestMailableWithTemplate::class,
    ]);

    $mailable = new TestMailableWithTemplate();

    Mail::to('john@example.com')->send($mailable);

    Mail::assertSent(function (TestMailableWithTemplate $mail) {
        test()->assertTrue($mail->hasCc('jane@example.com'));
        test()->assertTrue($mail->hasBcc('tarzan@example.com'));

        return true;
    });
});

it('will can use replacers to replace content', function () {
    /** @var TransactionalMailTemplate $template */
    $template = TransactionalMailTemplate::factory()->create([
        'name' => 'test-template',
        'body' => 'test html ::argument::',
        'test_using_mailable' => TestMailableWithTemplate::class,
        'replacers' => ['test'],
    ]);

    config()->set('mailcoach.transactional.replacers', [
        'test' => TestTransactionalMailReplacer::class,
    ]);

    $mailable = $template->getMailable();

    test()->assertStringContainsString('test html test-argument-from-replacer', $mailable->render());
});
