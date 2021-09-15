<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Database\Factories\UserFactory;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Mails\ImportSubscribersResultMail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Domain\Campaign\Mails\WelcomeMail;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\ImportSubscribersController;
use Spatie\Mailcoach\Tests\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

uses(TestCase::class);

beforeEach(function () {
    test()->emailList = EmailList::factory()->create();

    test()->user = UserFactory::new()->create();
    test()->actingAs(test()->user);

    Mail::fake();
});

it('can subscribe multiple emails in one go', function () {
    test()->withoutExceptionHandling();

    uploadStub('valid-and-invalid.csv');

    test()->assertCount(3, test()->emailList->subscribers);

    foreach (['freek@spatie.be', 'willem@spatie.be', 'rias@spatie.be'] as $email) {
        test()->assertEquals(SubscriptionStatus::SUBSCRIBED, test()->emailList->getSubscriptionStatus($email));
    }

    $subscriberImport = SubscriberImport::first();

    test()->assertEquals(3, $subscriberImport->imported_subscribers_count);
    test()->assertEquals(1, $subscriberImport->error_count);

    Mail::assertSent(ImportSubscribersResultMail::class, function (ImportSubscribersResultMail $mail) use ($subscriberImport) {
        test()->assertTrue($mail->hasTo(test()->user->email));
        test()->assertEquals($subscriberImport->id, $mail->subscriberImport->id);

        return true;
    });

    Mail::assertNotQueued(WelcomeMail::class);
    Mail::assertNotSent(WelcomeMail::class);
});

it('will fill the correct attributes', function () {
    uploadStub('single.csv');

    test()->assertCount(1, test()->emailList->subscribers);
    test()->assertTrue(test()->emailList->isSubscribed('john@example.com'));

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
    $subscriber = Subscriber::first();
    test()->assertEquals('John', $subscriber->first_name);
    test()->assertEquals('Doe', $subscriber->last_name);
    test()->assertEquals('Developer', $subscriber->extra_attributes->job_title);
});

it('will subscribe the emails immediately even if the list requires confirmation', function () {
    test()->emailList->update(['requires_confirmation' => true]);

    uploadStub('single.csv');

    test()->assertTrue(test()->emailList->isSubscribed('john@example.com'));
});

it('will trim the subscriber row values', function () {
    uploadStub('with-whitespace.csv');

    $subscriber = Subscriber::findForEmail('john@example.com', test()->emailList);

    test()->assertNotEmpty($subscriber);
    test()->assertEquals('John', $subscriber->first_name);
    test()->assertEquals('Doe', $subscriber->last_name);
    test()->assertEquals('Developer', $subscriber->extra_attributes->job_title);
});

it('will not import a subscriber that is already on the list', function () {
    Subscriber::createWithEmail('john@example.com')
        ->skipConfirmation()
        ->doNotSendWelcomeMail()
        ->subscribeTo(test()->emailList);

    uploadStub('single.csv');
    uploadStub('single.csv');
    uploadStub('single.csv');

    test()->assertTrue(test()->emailList->isSubscribed('john@example.com'));

    test()->assertCount(1, Subscriber::all());
});

it('can import tags', function () {
    uploadStub('single.csv');

    $subscriber = Subscriber::findForEmail('john@example.com', test()->emailList);

    test()->assertEquals(['tag1', 'tag2'], $subscriber->tags()->pluck('name')->toArray());
});

it('will remove existing tags if replace tags is enabled', function () {
    $subscriber = test()->emailList->subscribeSkippingConfirmation('john@example.com');

    $subscriber->addTag('previousTag');

    uploadStub('single.csv', [
        'replace_tags' => 'replace',
    ]);

    $subscriber = Subscriber::findForEmail('john@example.com', test()->emailList);

    test()->assertEquals(['tag1', 'tag2'], $subscriber->tags()->pluck('name')->toArray());
});

it('will not remove existing tags if replace tags is disabled', function () {
    $subscriber = test()->emailList->subscribeSkippingConfirmation('john@example.com');

    $subscriber->addTag('previousTag');

    // replace_tags is false by default.
    uploadStub('single.csv');

    $subscriber = Subscriber::findForEmail('john@example.com', test()->emailList);

    test()->assertEquals(['previousTag', 'tag1', 'tag2'], $subscriber->tags()->pluck('name')->toArray());
});

test('by default it will not subscribe a subscriber that has unsubscribed to the list before', function () {
    test()->emailList->subscribeSkippingConfirmation('john@example.com');
    test()->emailList->unsubscribe('john@example.com');

    uploadStub('single.csv');

    test()->assertFalse(test()->emailList->isSubscribed('john@example.com'));
    test()->assertCount(0, test()->emailList->subscribers);
    test()->assertEquals(1, SubscriberImport::first()->error_count);
});

it('can subscribe subscribers that were unsubscribed before', function () {
    test()->emailList->subscribeSkippingConfirmation('john@example.com');
    test()->emailList->unsubscribe('john@example.com');

    uploadStub('single.csv', ['subscribe_unsubscribed' => true]);

    test()->assertTrue(test()->emailList->isSubscribed('john@example.com'));
    test()->assertCount(1, test()->emailList->subscribers);
    test()->assertEquals(0, SubscriberImport::first()->error_count);
});

test('by default it will not unsubscribe any existing subscribers', function () {
    test()->emailList->subscribeSkippingConfirmation('paul@example.com');

    uploadStub('single.csv');

    test()->assertTrue(test()->emailList->isSubscribed('john@example.com'));
    test()->assertTrue(test()->emailList->isSubscribed('paul@example.com'));
});

it('can unsubscribe any existing subscribers that were not part of the import', function () {
    test()->emailList->subscribeSkippingConfirmation('paul@example.com');

    uploadStub('single.csv', ['unsubscribe_others' => true]);

    test()->assertTrue(test()->emailList->isSubscribed('john@example.com'));
    test()->assertFalse(test()->emailList->isSubscribed('paul@example.com'));
});

it('can handle an empty file', function () {
    uploadStub('empty.csv');

    test()->assertCount(0, test()->emailList->subscribers);
});

it('can handle an invalid file', function () {
    uploadStub('invalid.csv');

    test()->assertCount(0, test()->emailList->subscribers);
});

it('can handle an xlsx file', function () {
    uploadStub(
        'excel.xlsx',
        [],
        'excel.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    );

    test()->assertCount(1, test()->emailList->subscribers);
});

// Helpers
function uploadStub(string $stubName, array $parameters = [], string $asFilename = 'import.csv', string $asMimetype = 'text/csv')
{
    $stubPath = test()->getStubPath($stubName);
    $tempPath = test()->getTempPath($stubName);

    File::copy($stubPath, $tempPath);

    $fileUpload = new UploadedFile(
        $tempPath,
        $asFilename,
        $asMimetype,
        filesize($stubPath)
    );

    test()->call(
        'post',
        action([ImportSubscribersController::class, 'import'], test()->emailList),
        $parameters,
        [],
        ['file' => $fileUpload]
    );
}

function getStubPath(string $name): string
{
    return __DIR__ . '/stubs/' . $name;
}

function getTempPath(string $name): string
{
    return __DIR__ . '/temp/' . $name;
}
