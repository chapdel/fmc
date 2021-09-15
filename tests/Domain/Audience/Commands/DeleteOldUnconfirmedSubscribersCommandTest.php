<?php

use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Audience\Commands\DeleteOldUnconfirmedSubscribersCommand;
use Spatie\Mailcoach\Domain\Audience\Enums\SubscriptionStatus;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

uses(TestCase::class);

beforeEach(function () {
    TestTime::freeze('Y-m-d H:i:s', '2019-01-01 00:00:00');

    test()->emailList = EmailList::factory()->create(['requires_confirmation' => true]);
});

it('will delete all unconfirmed subscribers that are older than a month', function () {
    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);
    test()->assertEquals(SubscriptionStatus::UNCONFIRMED, $subscriber->status);

    TestTime::addMonth();
    test()->artisan(DeleteOldUnconfirmedSubscribersCommand::class)->assertExitCode(0);
    test()->assertCount(1, Subscriber::all());

    TestTime::addSecond();
    test()->artisan(DeleteOldUnconfirmedSubscribersCommand::class)->assertExitCode(0);
    test()->assertCount(0, Subscriber::all());
});

it('will not delete confirmed subscribers', function () {
    $subscriber = Subscriber::createWithEmail('john@example.com')->skipConfirmation()->subscribeTo(test()->emailList);
    test()->assertEquals(SubscriptionStatus::SUBSCRIBED, $subscriber->status);

    TestTime::addMonth()->addSecond();
    test()->artisan(DeleteOldUnconfirmedSubscribersCommand::class)->assertExitCode(0);
    test()->assertCount(1, Subscriber::all());
});

it('will detach all tags when deleting a subscriber', function () {
    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);

    $subscriber->addTag('test');

    TestTime::addMonth()->addSecond();

    test()->artisan(DeleteOldUnconfirmedSubscribersCommand::class)->assertExitCode(0);

    test()->assertCount(0, Subscriber::all());
    test()->assertCount(0, DB::table('mailcoach_email_list_subscriber_tags')->get());
    test()->assertCount(1, Tag::all());
});
