<?php


namespace Spatie\Mailcoach\Tests\Domain\TransactionalMail\Concerns;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestMailableWithTemplate;
use Spatie\Mailcoach\Tests\TestClasses\TestTransactionalMailReplacer;

class UsesMailcoachTemplateTest extends TestCase
{
    /** @test */
    public function it_can_render_the_template_containing_blade_variables()
    {
        /** @var TransactionalMailTemplate $template */
        $template = TransactionalMailTemplate::factory()->create([
            'name' => 'test-template',
            'body' => 'test html {{ $argument }}',
            'test_using_mailable' => TestMailableWithTemplate::class,
            'type' => 'blade',
        ]);

        $mailable = $template->getMailable();

        $this->assertStringContainsString('test html test-argument', $mailable->render());
    }

    /** @test */
    public function it_can_render_a_template_containing_markdown_and_blade_variables()
    {
        /** @var TransactionalMailTemplate $template */
        $template = TransactionalMailTemplate::factory()->create([
            'name' => 'test-template',
            'body' => file_get_contents(__DIR__ . '/stubs/blade-markdown.blade.php'),
            'test_using_mailable' => TestMailableWithTemplate::class,
            'type' => 'blade-markdown',
        ]);

        $mailable = $template->getMailable();
dd($mailable->render());
        $this->assertStringContainsString('Hi all', $mailable->render());
    }

    /** @test */
    public function it_will_not_compile_blade_if_it_is_not_allowed()
    {
        /** @var TransactionalMailTemplate $template */
        $template = TransactionalMailTemplate::factory()->create([
            'name' => 'test-template',
            'body' => 'test html {{ $argument }}',
            'test_using_mailable' => TestMailableWithTemplate::class,
            'type' => 'html',
        ]);

        $mailable = $template->getMailable();

        $this->assertStringContainsString('test html {{ $argument }}', $mailable->render());
    }

    /** @test */
    public function it_will_use_cc_and_bcc_when_sending_out_a_mail_using_the_template()
    {
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
            $this->assertTrue($mail->hasCc('jane@example.com'));
            $this->assertTrue($mail->hasBcc('tarzan@example.com'));

            return true;
        });
    }

    /** @test */
    public function it_will_can_use_replacers_to_replace_content()
    {
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

        $this->assertStringContainsString('test html test-argument-from-replacer', $mailable->render());
    }
}
