<?php


namespace Spatie\Mailcoach\Tests\Domain\TransactionalMail\Concerns;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestMailableWithTemplate;

class UsesMailcoachTemplateTest extends TestCase
{
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

        Mail::assertSent(function(TestMailableWithTemplate $mail) {
            $this->assertTrue($mail->hasCc('jane@example.com'));
            $this->assertTrue($mail->hasBcc('tarzan@example.com'));

            return true;
        });
    }

    /** @test */
    public function it_can_render_the_template_containing_blade_variables()
    {
        /** @var TransactionalMailTemplate $template */
        $template = TransactionalMailTemplate::factory()->create([
            'name' => 'test-template',
            'body' => 'test html {{ $argument }}',
            'test_using_mailable' => TestMailableWithTemplate::class,
        ]);

        $mailable = $template->getMailable();

        $this->assertStringContainsString('test html test-argument', $mailable->render());
    }
}
