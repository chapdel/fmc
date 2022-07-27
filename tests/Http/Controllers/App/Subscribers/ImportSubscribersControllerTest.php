<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Database\Factories\UserFactory;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Mails\ImportSubscribersResultMail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\ImportSubscribersController;
use Symfony\Component\HttpFoundation\File\UploadedFile;

beforeEach(function () {
    test()->emailList = EmailList::factory()->create();

    test()->user = UserFactory::new()->create();
    test()->actingAs(test()->user);

    Mail::fake();
});

it('can subscribe multiple emails in one go', function () {
    test()->withoutExceptionHandling();

    uploadStub('valid-and-invalid.csv');

    expect(test()->emailList->subscribers)->toHaveCount(3);

    foreach (['freek@spatie.be', 'willem@spatie.be', 'rias@spatie.be'] as $email) {
        expect(test()->emailList->getSubscriptionStatus($email))->toEqual(SubscriptionStatus::Subscribed);
    }

    $subscriberImport = SubscriberImport::first();

    expect($subscriberImport->imported_subscribers_count)->toEqual(4);
    expect($subscriberImport->subscribers()->count())->toEqual(3);
    expect(count($subscriberImport->errors))->toEqual(1);

    Mail::assertSent(ImportSubscribersResultMail::class, function (ImportSubscribersResultMail $mail) use ($subscriberImport) {
        expect($mail->hasTo(test()->user->email))->toBeTrue();
        expect($mail->subscriberImport->id)->toEqual($subscriberImport->id);

        return true;
    });
});

it('will fill the correct attributes', function () {
    uploadStub('single.csv');

    expect(test()->emailList->subscribers)->toHaveCount(1);
    expect(test()->emailList->isSubscribed('john@example.com'))->toBeTrue();

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\Subscriber $subscriber */
    $subscriber = Subscriber::first();
    expect($subscriber->first_name)->toEqual('John');
    expect($subscriber->last_name)->toEqual('Doe');
    expect($subscriber->extra_attributes->job_title)->toEqual('Developer');
});

it('will subscribe the emails immediately even if the list requires confirmation', function () {
    test()->emailList->update(['requires_confirmation' => true]);

    uploadStub('single.csv');

    expect(test()->emailList->isSubscribed('john@example.com'))->toBeTrue();
});

it('will trim the subscriber row values', function () {
    uploadStub('with-whitespace.csv');

    $subscriber = Subscriber::findForEmail('john@example.com', test()->emailList);

    test()->assertNotEmpty($subscriber);
    expect($subscriber->first_name)->toEqual('John');
    expect($subscriber->last_name)->toEqual('Doe');
    expect($subscriber->extra_attributes->job_title)->toEqual('Developer');
});

it('will not import a subscriber that is already on the list', function () {
    Subscriber::createWithEmail('john@example.com')
        ->skipConfirmation()
        ->subscribeTo(test()->emailList);

    uploadStub('single.csv');
    uploadStub('single.csv');
    uploadStub('single.csv');

    expect(test()->emailList->isSubscribed('john@example.com'))->toBeTrue();

    expect(Subscriber::all())->toHaveCount(1);
});

it('can import tags', function () {
    uploadStub('single.csv');

    $subscriber = Subscriber::findForEmail('john@example.com', test()->emailList);

    expect($subscriber->tags()->pluck('name')->toArray())->toEqual(['tag1', 'tag2']);
});

it('will remove existing tags if replace tags is enabled', function () {
    $subscriber = test()->emailList->subscribeSkippingConfirmation('john@example.com');

    $subscriber->addTag('previousTag');

    uploadStub('single.csv', [
        'replaceTags' => 'replace',
    ]);

    $subscriber = Subscriber::findForEmail('john@example.com', test()->emailList);

    expect($subscriber->tags()->pluck('name')->toArray())->toEqual(['tag1', 'tag2']);
});

it('will not remove existing tags if replace tags is disabled', function () {
    $subscriber = test()->emailList->subscribeSkippingConfirmation('john@example.com');

    $subscriber->addTag('previousTag');

    // replaceTags is false by default.
    uploadStub('single.csv');

    $subscriber = Subscriber::findForEmail('john@example.com', test()->emailList);

    expect($subscriber->tags()->pluck('name')->toArray())->toEqual(['previousTag', 'tag1', 'tag2']);
});

test('by default it will not subscribe a subscriber that has unsubscribed to the list before', function () {
    test()->emailList->subscribeSkippingConfirmation('john@example.com');
    test()->emailList->unsubscribe('john@example.com');

    uploadStub('single.csv');

    expect(test()->emailList->isSubscribed('john@example.com'))->toBeFalse();
    expect(test()->emailList->subscribers)->toHaveCount(0);
    expect(count(SubscriberImport::first()->errors))->toEqual(1);
});

it('can subscribe subscribers that were unsubscribed before', function () {
    test()->emailList->subscribeSkippingConfirmation('john@example.com');
    test()->emailList->unsubscribe('john@example.com');

    uploadStub('single.csv', ['subscribeUnsubscribed' => true]);

    expect(test()->emailList->isSubscribed('john@example.com'))->toBeTrue();
    expect(test()->emailList->subscribers)->toHaveCount(1);
    expect(SubscriberImport::first()->error_count)->toEqual(0);
});

test('by default it will not unsubscribe any existing subscribers', function () {
    test()->emailList->subscribeSkippingConfirmation('paul@example.com');

    uploadStub('single.csv');

    expect(test()->emailList->isSubscribed('john@example.com'))->toBeTrue();
    expect(test()->emailList->isSubscribed('paul@example.com'))->toBeTrue();
});

it('can unsubscribe any existing subscribers that were not part of the import', function () {
    test()->emailList->subscribeSkippingConfirmation('paul@example.com');

    uploadStub('single.csv', ['unsubscribeMissing' => true]);

    expect(test()->emailList->isSubscribed('john@example.com'))->toBeTrue();
    expect(test()->emailList->isSubscribed('paul@example.com'))->toBeFalse();
});

it('can handle an empty file', function () {
    uploadStub('empty.csv');

    expect(test()->emailList->subscribers)->toHaveCount(0);
});

it('can handle an invalid file', function () {
    uploadStub('invalid.csv');

    expect(test()->emailList->subscribers)->toHaveCount(0);
});

it('can handle an xlsx file', function () {
    uploadStub(
        'excel.xlsx',
        [],
        'excel.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    );

    expect(test()->emailList->subscribers)->toHaveCount(1);
});

// Helpers
function uploadStub(string $stubName, array $parameters = [], string $asFilename = 'import.csv', string $asMimetype = 'text/csv')
{
    $stubPath = test()->getStubPath($stubName);
    $tempPath = test()->getTempPath($stubName);

    File::copy($stubPath, $tempPath);

    $file = \Illuminate\Http\UploadedFile::fake()
        ->createWithContent($asFilename, file_get_contents($tempPath));

    \Livewire\Livewire::test('mailcoach::subscriber-imports', ['emailList' => test()->emailList])
        ->set('file', $file)
        ->set($parameters)
        ->call('upload');
}

function getStubPath(string $name): string
{
    return __DIR__ . '/stubs/' . $name;
}

function getTempPath(string $name): string
{
    return __DIR__ . '/temp/' . $name;
}
