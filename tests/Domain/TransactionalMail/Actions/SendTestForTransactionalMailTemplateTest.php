<?php

namespace Spatie\Mailcoach\Tests\Domain\TransactionalMail\Actions;

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\TransactionalMail\Actions\SendTestForTransactionalMailTemplateAction;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestMailableWithTemplate;

class SendTestForTransactionalMailTemplateTest extends TestCase
{
    /** @test */
    public function it_can_send_a_test_for_a_transactional_mail_template()
    {
        Mail::fake();

        $template = TransactionalMailTemplate::factory()->create([
            'name' => 'test-template',
            'body' => 'test html {{ $argument }}',
            'test_using_mailable' => TestMailableWithTemplate::class,
        ]);

        (new SendTestForTransactionalMailTemplateAction())->execute(
            ['john@example.com'],
            $template,
        );

        Mail::assertSent(function (TestMailableWithTemplate $mail) {
            $this->assertTrue($mail->hasTo('john@example.com'));

            return true;
        });
    }

    /** @test */
    public function it_will_not_use_cc_or_bcc_when_sending_out_a_test()
    {
        Mail::fake();

        $template = TransactionalMailTemplate::factory()->create([
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
            $this->assertFalse($mail->hasCc('jane@example.com'));
            $this->assertFalse($mail->hasBcc('tarzan@example.com'));

            return true;
        });
    }
}
