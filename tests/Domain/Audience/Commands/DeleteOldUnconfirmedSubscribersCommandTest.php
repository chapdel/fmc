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
    expect($subscriber->status)->toEqual(SubscriptionStatus::UNCONFIRMED);

    TestTime::addMonth();
    test()->artisan(DeleteOldUnconfirmedSubscribersCommand::class)->assertExitCode(0);
    expect(Subscriber::all())->toHaveCount(1);

    TestTime::addSecond();
    test()->artisan(DeleteOldUnconfirmedSubscribersCommand::class)->assertExitCode(0);
    expect(Subscriber::all())->toHaveCount(0);
});

it('will not delete confirmed subscribers', function () {
    $subscriber = Subscriber::createWithEmail('john@example.com')->skipConfirmation()->subscribeTo(test()->emailList);
    expect($subscriber->status)->toEqual(SubscriptionStatus::SUBSCRIBED);

    TestTime::addMonth()->addSecond();
    test()->artisan(DeleteOldUnconfirmedSubscribersCommand::class)->assertExitCode(0);
    expect(Subscriber::all())->toHaveCount(1);
});

it('will detach all tags when deleting a subscriber', function () {
    $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo(test()->emailList);

    $subscriber->addTag('test');

    TestTime::addMonth()->addSecond();

    test()->artisan(DeleteOldUnconfirmedSubscribersCommand::class)->assertExitCode(0);

    expect(Subscriber::all())->toHaveCount(0);
    expect(DB::table('mailcoach_email_list_subscriber_tags')->get())->toHaveCount(0);
    expect(Tag::all())->toHaveCount(1);
});
