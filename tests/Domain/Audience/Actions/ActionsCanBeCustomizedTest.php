<?php

use Spatie\Mailcoach\Database\Factories\UserFactory;
use Spatie\Mailcoach\Domain\Audience\Jobs\ImportSubscribersJob;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\InvalidConfig;
use Spatie\Mailcoach\Tests\TestClasses\CustomConfirmSubscriberAction;
use Spatie\Mailcoach\Tests\TestClasses\CustomCreateSubscriberAction;
use Spatie\Mailcoach\Tests\TestClasses\CustomImportSubscribersAction;

test('the create subscriber action can be customized', function () {
    config()->set('mailcoach.audience.actions.create_subscriber', CustomCreateSubscriberAction::class);

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */
    $emailList = EmailList::factory()->create();

    $subscriber = $emailList->subscribe('john@example.com');

    expect($subscriber->email)->toEqual('overridden@example.com');
});

test('the confirm subscription class can be customized', function () {
    config()->set('mailcoach.audience.actions.confirm_subscriber', CustomConfirmSubscriberAction::class);

    $emailList = EmailList::factory()->create([
        'requires_confirmation' => true,
    ]);

    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);

    $subscriber->confirm();

    expect($subscriber->email)->toEqual('overridden@example.com');
});

test('a wrongly configured class will result in an exception', function () {
    config()->set('mailcoach.audience.actions.create_subscriber', 'invalid-action');

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList $emailList */
    $emailList = EmailList::factory()->create();

    test()->expectException(InvalidConfig::class);

    $emailList->subscribe('john@example.com');
});

test('the import subscribers class can be customized', function () {
    config()->set('mailcoach.audience.actions.import_subscribers', CustomImportSubscribersAction::class);

    $subscriberImport = SubscriberImport::factory()->create();

    $user = UserFactory::new()->create();

    test()->expectExceptionMessage('Inside custom import action');

    dispatch(new ImportSubscribersJob($subscriberImport, $user));
});
