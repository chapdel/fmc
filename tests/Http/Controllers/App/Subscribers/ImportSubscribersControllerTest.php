<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\App\Subscribers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Database\Factories\UserFactory;
use Spatie\Mailcoach\Domain\Campaign\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Campaign\Mails\ImportSubscribersResultMail;
use Spatie\Mailcoach\Domain\Campaign\Mails\WelcomeMail;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Models\SubscriberImport;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\ImportSubscribersController;
use Spatie\Mailcoach\Tests\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportSubscribersControllerTest extends TestCase
{
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\EmailList */
    protected $emailList;

    public function setUp(): void
    {
        parent::setUp();

        $this->emailList = EmailList::factory()->create();

        $this->user = UserFactory::new()->create();
        $this->actingAs($this->user);

        Mail::fake();
    }

    /** @test */
    public function it_can_subscribe_multiple_emails_in_one_go()
    {
        $this->uploadStub('valid-and-invalid.csv');

        $this->assertCount(3, $this->emailList->subscribers);

        foreach (['freek@spatie.be', 'willem@spatie.be', 'rias@spatie.be'] as $email) {
            $this->assertEquals(SubscriptionStatus::SUBSCRIBED, $this->emailList->getSubscriptionStatus($email));
        }

        $subscriberImport = SubscriberImport::first();

        $this->assertEquals(3, $subscriberImport->imported_subscribers_count);
        $this->assertEquals(1, $subscriberImport->error_count);

        Mail::assertSent(ImportSubscribersResultMail::class, function (ImportSubscribersResultMail $mail) use ($subscriberImport) {
            $this->assertTrue($mail->hasTo($this->user->email));
            $this->assertEquals($subscriberImport->id, $mail->subscriberImport->id);

            return true;
        });

        Mail::assertNotQueued(WelcomeMail::class);
        Mail::assertNotSent(WelcomeMail::class);
    }

    /** @test */
    public function it_will_fill_the_correct_attributes()
    {
        $this->uploadStub('single.csv');

        $this->assertCount(1, $this->emailList->subscribers);
        $this->assertTrue($this->emailList->isSubscribed('john@example.com'));

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Subscriber $subscriber */
        $subscriber = Subscriber::first();
        $this->assertEquals('John', $subscriber->first_name);
        $this->assertEquals('Doe', $subscriber->last_name);
        $this->assertEquals('Developer', $subscriber->extra_attributes->job_title);
    }

    /** @test */
    public function it_will_subscribe_the_emails_immediately_even_if_the_list_requires_confirmation()
    {
        $this->emailList->update(['requires_confirmation' => true]);

        $this->uploadStub('single.csv');

        $this->assertTrue($this->emailList->isSubscribed('john@example.com'));
    }

    /** @test */
    public function it_will_trim_the_subscriber_row_values()
    {
        $this->uploadStub('with-whitespace.csv');

        $subscriber = Subscriber::findForEmail('john@example.com', $this->emailList);

        $this->assertNotEmpty($subscriber);
        $this->assertEquals('John', $subscriber->first_name);
        $this->assertEquals('Doe', $subscriber->last_name);
        $this->assertEquals('Developer', $subscriber->extra_attributes->job_title);
    }

    /** @test */
    public function it_will_not_import_a_subscriber_that_is_already_on_the_list()
    {
        Subscriber::createWithEmail('john@example.com')
            ->skipConfirmation()
            ->doNotSendWelcomeMail()
            ->subscribeTo($this->emailList);

        $this->uploadStub('single.csv');
        $this->uploadStub('single.csv');
        $this->uploadStub('single.csv');

        $this->assertTrue($this->emailList->isSubscribed('john@example.com'));

        $this->assertCount(1, Subscriber::all());
    }

    /** @test */
    public function it_can_import_tags()
    {
        $this->uploadStub('single.csv');

        $subscriber = Subscriber::findForEmail('john@example.com', $this->emailList);

        $this->assertEquals(['tag1', 'tag2'], $subscriber->tags()->pluck('name')->toArray());
    }

    /** @test */
    public function it_will_remove_existing_tags_if_replace_tags_is_enabled()
    {
        $subscriber = $this->emailList->subscribeSkippingConfirmation('john@example.com');

        $subscriber->addTag('previousTag');

        $this->uploadStub('single.csv', [
            'replace_tags' => true,
        ]);

        $subscriber = Subscriber::findForEmail('john@example.com', $this->emailList);

        $this->assertEquals(['tag1', 'tag2'], $subscriber->tags()->pluck('name')->toArray());
    }

    /** @test */
    public function it_will_not_remove_existing_tags_if_replace_tags_is_disabled()
    {
        $subscriber = $this->emailList->subscribeSkippingConfirmation('john@example.com');

        $subscriber->addTag('previousTag');

        // replace_tags is false by default.
        $this->uploadStub('single.csv');

        $subscriber = Subscriber::findForEmail('john@example.com', $this->emailList);

        $this->assertEquals(['previousTag', 'tag1', 'tag2'], $subscriber->tags()->pluck('name')->toArray());
    }

    /** @test */
    public function by_default_it_will_not_subscribe_a_subscriber_that_has_unsubscribed_to_the_list_before()
    {
        $this->emailList->subscribeSkippingConfirmation('john@example.com');
        $this->emailList->unsubscribe('john@example.com');

        $this->uploadStub('single.csv');

        $this->assertFalse($this->emailList->isSubscribed('john@example.com'));
        $this->assertCount(0, $this->emailList->subscribers);
        $this->assertEquals(1, SubscriberImport::first()->error_count);
    }

    /** @test */
    public function it_can_subscribe_subscribers_that_were_unsubscribed_before()
    {
        $this->emailList->subscribeSkippingConfirmation('john@example.com');
        $this->emailList->unsubscribe('john@example.com');

        $this->uploadStub('single.csv', ['subscribe_unsubscribed' => true]);

        $this->assertTrue($this->emailList->isSubscribed('john@example.com'));
        $this->assertCount(1, $this->emailList->subscribers);
        $this->assertEquals(0, SubscriberImport::first()->error_count);
    }

    /** @test */
    public function by_default_it_will_not_unsubscribe_any_existing_subscribers()
    {
        $this->emailList->subscribeSkippingConfirmation('paul@example.com');

        $this->uploadStub('single.csv');

        $this->assertTrue($this->emailList->isSubscribed('john@example.com'));
        $this->assertTrue($this->emailList->isSubscribed('paul@example.com'));
    }

    /** @test */
    public function it_can_unsubscribe_any_existing_subscribers_that_were_not_part_of_the_import()
    {
        $this->emailList->subscribeSkippingConfirmation('paul@example.com');

        $this->uploadStub('single.csv', ['unsubscribe_others' => true]);

        $this->assertTrue($this->emailList->isSubscribed('john@example.com'));
        $this->assertFalse($this->emailList->isSubscribed('paul@example.com'));
    }

    /** @test */
    public function it_can_handle_an_empty_file()
    {
        $this->uploadStub('empty.csv');

        $this->assertCount(0, $this->emailList->subscribers);
    }

    /** @test */
    public function it_can_handle_an_invalid_file()
    {
        $this->uploadStub('invalid.csv');

        $this->assertCount(0, $this->emailList->subscribers);
    }

    /** @test */
    public function it_can_handle_an_xlsx_file()
    {
        $this->uploadStub(
            'excel.xlsx',
            [],
            'excel.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );

        $this->assertCount(1, $this->emailList->subscribers);
    }

    private function uploadStub(string $stubName, array $parameters = [], string $asFilename = 'import.csv', string $asMimetype = 'text/csv')
    {
        $stubPath = $this->getStubPath($stubName);
        $tempPath = $this->getTempPath($stubName);

        File::copy($stubPath, $tempPath);

        $fileUpload = new UploadedFile(
            $tempPath,
            $asFilename,
            $asMimetype,
            filesize($stubPath)
        );

        $this->call(
            'post',
            action([ImportSubscribersController::class, 'import'], $this->emailList),
            $parameters,
            [],
            ['file' => $fileUpload]
        );
    }

    private function getStubPath(string $name): string
    {
        return __DIR__ . '/stubs/' . $name;
    }

    private function getTempPath(string $name): string
    {
        return __DIR__ . '/temp/' . $name;
    }
}
