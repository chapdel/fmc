<?php

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\EverySubscriberSegment;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotSendCampaign;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignTestJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\TestCustomInstanciatedQueryOnlyShouldSendToJohn;
use Spatie\Mailcoach\Tests\TestClasses\TestCustomQueryOnlyShouldSendToJohn;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMail;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMailWithArguments;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMailWithStaticHtml;
use Spatie\Snapshots\MatchesSnapshots;
use Spatie\TestTime\TestTime;

uses(TestCase::class);
uses(MatchesSnapshots::class);

beforeEach(function () {
    Queue::fake();

    test()->campaign = Campaign::create()->refresh();
});

test('the default status is draft', function () {
    test()->assertEquals(CampaignStatus::DRAFT, test()->campaign->status);
});

it('can set a from email', function () {
    test()->campaign->from('sender@example.com');

    test()->assertEquals('sender@example.com', test()->campaign->from_email);
});

it('can set both a from email and a from name', function () {
    test()->campaign->from('sender@example.com', 'Sender name');

    test()->assertEquals('sender@example.com', test()->campaign->from_email);
    test()->assertEquals('Sender name', test()->campaign->from_name);
});

it('can be marked to track opens', function () {
    test()->assertFalse(test()->campaign->track_opens);

    test()->campaign->trackOpens();

    test()->assertTrue(test()->campaign->refresh()->track_opens);
});

it('can be marked to track clicks', function () {
    test()->assertFalse(test()->campaign->track_clicks);

    test()->campaign->trackClicks();

    test()->assertTrue(test()->campaign->refresh()->track_clicks);
});

it('can add a subject', function () {
    test()->assertNull(test()->campaign->subject);

    test()->campaign->subject('hello');

    test()->assertEquals('hello', test()->campaign->refresh()->subject);
});

it('can add a list', function () {
    $list = EmailList::factory()->create();

    test()->campaign->to($list);

    test()->assertEquals($list->id, test()->campaign->refresh()->email_list_id);
});

it('can be sent', function () {
    $list = EmailList::factory()->create();

    $campaign = Campaign::create()
        ->from('test@example.com')
        ->subject('test')
        ->content('my content')
        ->to($list)
        ->send();

    Queue::assertPushed(SendCampaignJob::class, function (SendCampaignJob $job) use ($campaign) {
        test()->assertEquals($campaign->id, $job->campaign->id);

        return true;
    });
});

it('has a shorthand to set the list and send it in one go', function () {
    $list = EmailList::factory()->create();

    $campaign = Campaign::create()
        ->from('test@example.com')
        ->content('my content')
        ->subject('test')
        ->sendTo($list);

    test()->assertEquals($list->id, $campaign->refresh()->email_list_id);

    Queue::assertPushed(SendCampaignJob::class, function (SendCampaignJob $job) use ($campaign) {
        test()->assertEquals($campaign->id, $job->campaign->id);

        return true;
    });
});

test('a mailable can be set', function () {
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */
    $campaign = Campaign::create()->useMailable(TestMailcoachMail::class);

    test()->assertEquals(TestMailcoachMail::class, $campaign->mailable_class);
});

test('a mailable can set campaign html', function () {
    $campaign = (new CampaignFactory())->create(['html' => 'null']);

    $mailable = (new TestMailcoachMail())->setSendable($campaign);
    app()->instance(TestMailcoachMail::class, $mailable);

    $campaign->useMailable(TestMailcoachMail::class);
    $campaign->content($campaign->contentFromMailable());

    test()->assertEquals(app(TestMailcoachMail::class)->viewHtml, $campaign->html);
});

it('will throw an exception when use an invalid mailable class', function () {
    test()->expectException(CouldNotSendCampaign::class);

    Campaign::create()->useMailable(static::class);
});

test('a segment can be set', function () {
    $campaign = Campaign::create()
        ->segment(TestCustomQueryOnlyShouldSendToJohn::class);

    test()->assertEquals(serialize(TestCustomQueryOnlyShouldSendToJohn::class), $campaign->segment_class);
});

test('an instantiated segment class can be set', function () {
    $segment = new TestCustomInstanciatedQueryOnlyShouldSendToJohn('john@example.com');
    $campaign = Campaign::create()
        ->segment($segment);

    test()->assertEquals(serialize($segment), $campaign->segment_class);
    test()->assertEquals('john@example.com', $campaign->getSegment()->email);
});

it('will throw an exception when use an invalid segment class', function () {
    test()->expectException(CouldNotSendCampaign::class);

    Campaign::create()->segment(static::class);
});

test('html and content are not required when sending a mailable', function () {
    Bus::fake();

    $list = EmailList::factory()->create();

    Campaign::create()
        ->from('test@example.com')
        ->content('my content')
        ->subject('test')
        ->sendTo($list);

    Bus::assertDispatched(SendCampaignJob::class);
});

it('can use the default from email and name set on the email list', function () {
    Bus::fake();

    $list = EmailList::factory()->create([
        'default_from_email' => 'defaultEmailList@example.com',
        'default_from_name' => 'List name',
    ]);

    Campaign::create()
        ->content('my content')
        ->subject('test')
        ->sendTo($list);

    Bus::assertDispatched(SendCampaignJob::class, function (SendCampaignJob $job) {
        test()->assertEquals('defaultEmailList@example.com', $job->campaign->from_email);
        test()->assertEquals('List name', $job->campaign->from_name);

        return true;
    });
});

it('can use the default reply to email and name set on the email list', function () {
    Bus::fake();

    $list = EmailList::factory()->create([
        'default_reply_to_email' => 'defaultEmailList@example.com',
        'default_reply_to_name' => 'List name',
    ]);

    Campaign::create()
        ->content('my content')
        ->subject('test')
        ->sendTo($list);

    Bus::assertDispatched(SendCampaignJob::class, function (SendCampaignJob $job) {
        test()->assertEquals('defaultEmailList@example.com', $job->campaign->reply_to_email);
        test()->assertEquals('List name', $job->campaign->reply_to_name);

        return true;
    });
});

it('will prefer the email and from name from the campaign over the defaults set on the email list', function () {
    Bus::fake();

    $list = EmailList::factory()->create([
        'default_from_email' => 'defaultEmailList@example.com',
        'default_from_name' => 'List name',
    ]);

    Campaign::create()
        ->content('my content')
        ->subject('test')
        ->from('campaign@example.com', 'campaign from name')
        ->sendTo($list);

    Bus::assertDispatched(SendCampaignJob::class, function (SendCampaignJob $job) {
        test()->assertEquals('campaign@example.com', $job->campaign->from_email);
        test()->assertEquals('campaign from name', $job->campaign->from_name);

        return true;
    });
});

it('will prefer the email and reply to name from the campaign over the defaults set on the email list', function () {
    Bus::fake();

    $list = EmailList::factory()->create([
        'default_reply_to_email' => 'defaultEmailList@example.com',
        'default_reply_to_name' => 'List name',
    ]);

    Campaign::create()
        ->content('my content')
        ->subject('test')
        ->from('campaign@example.com', 'campaign from name')
        ->replyTo('replyToCampaign@example.com', 'reply to from campaign')
        ->sendTo($list);

    Bus::assertDispatched(SendCampaignJob::class, function (SendCampaignJob $job) {
        test()->assertEquals('replyToCampaign@example.com', $job->campaign->reply_to_email);
        test()->assertEquals('reply to from campaign', $job->campaign->reply_to_name);

        return true;
    });
});

it('has a scope that can get campaigns sent in a certain period', function () {
    $sentAt1430 = CampaignFactory::createSentAt('2019-01-01 14:30:00');
    $sentAt1530 = CampaignFactory::createSentAt('2019-01-01 15:30:00');
    $sentAt1630 = CampaignFactory::createSentAt('2019-01-01 16:30:00');
    $sentAt1730 = CampaignFactory::createSentAt('2019-01-01 17:30:00');

    $campaigns = Campaign::sentBetween(
        Carbon::createFromFormat('Y-m-d H:i:s', '2019-01-01 13:30:00'),
        Carbon::createFromFormat('Y-m-d H:i:s', '2019-01-01 17:30:00'),
    )->get();

    test()->assertEquals(
        [$sentAt1430->id, $sentAt1530->id, $sentAt1630->id],
        $campaigns->pluck('id')->values()->toArray(),
    );
});

it('can send out a test email', function () {
    Bus::fake();

    $email = 'john@example.com';

    test()->campaign->sendTestMail($email);

    Bus::assertDispatched(SendCampaignTestJob::class, function (SendCampaignTestJob $job) use ($email) {
        test()->assertEquals(test()->campaign->id, $job->campaign->id);
        test()->assertEquals($email, $job->email);

        return true;
    });
});

it('can send out multiple test emails at once', function () {
    Bus::fake();

    test()->campaign->sendTestMail(['john@example.com', 'paul@example.com']);

    Bus::assertDispatched(SendCampaignTestJob::class, fn (SendCampaignTestJob $job) => $job->email === 'john@example.com');

    Bus::assertDispatched(SendCampaignTestJob::class, fn (SendCampaignTestJob $job) => $job->email === 'paul@example.com');
});

it('can dispatch a job to recalculate statistics', function () {
    Bus::fake();

    test()->campaign->dispatchCalculateStatistics();

    Bus::assertDispatched(CalculateStatisticsJob::class, 1);
});

it('will not dispatch the recalculation job twice', function () {
    Bus::fake();

    test()->campaign->dispatchCalculateStatistics();
    test()->campaign->dispatchCalculateStatistics();

    Bus::assertDispatched(CalculateStatisticsJob::class, 1);
});

it('can dispatch the recalculation job again after the previous job has run', function () {
    Bus::fake();

    test()->campaign->dispatchCalculateStatistics();

    (new CalculateStatisticsJob(test()->campaign))->handle();

    test()->campaign->dispatchCalculateStatistics();

    Bus::assertDispatched(CalculateStatisticsJob::class, 2);
});

it('has scopes to get campaigns in various states', function () {
    Campaign::all()->each->delete();

    $draftCampaign = Campaign::factory()->create([
        'status' => CampaignStatus::DRAFT,
    ]);

    $scheduledInThePastCampaign = Campaign::factory()->create([
        'status' => CampaignStatus::DRAFT,
        'scheduled_at' => now()->subSecond(),
    ]);

    $scheduledNowCampaign = Campaign::factory()->create([
        'status' => CampaignStatus::DRAFT,
        'scheduled_at' => now(),
    ]);

    $scheduledInFutureCampaign = Campaign::factory()->create([
        'status' => CampaignStatus::DRAFT,
        'scheduled_at' => now()->addSecond(),
    ]);

    $sendingCampaign = Campaign::factory()->create([
        'status' => CampaignStatus::SENDING,
    ]);

    $sentCampaign = Campaign::factory()->create([
        'status' => CampaignStatus::SENT,
    ]);

    assertModels([
        $draftCampaign,
    ], Campaign::draft()->get());

    assertModels([
        $scheduledInThePastCampaign,
        $scheduledNowCampaign,
        $scheduledInFutureCampaign,
    ], Campaign::scheduled()->get());

    assertModels([
        $scheduledInThePastCampaign,
        $scheduledNowCampaign,
    ], Campaign::shouldBeSentNow()->get());

    assertModels([
        $sendingCampaign,
        $sentCampaign,
    ], Campaign::sendingOrSent()->get());
});

it('can send determine if it has troubles sending out mails', function () {
    TestTime::freeze();

    test()->assertFalse(test()->campaign->hasTroublesSendingOutMails());

    test()->campaign->update([
        'status' => CampaignStatus::SENDING,
        'last_modified_at' => now(),
    ]);

    SendFactory::new()->create([
        'campaign_id' => test()->campaign->id,
        'sent_at' => now(),
    ]);

    $send = SendFactory::new()->create([
        'campaign_id' => test()->campaign->id,
        'sent_at' => null,
    ]);

    test()->assertFalse(test()->campaign->hasTroublesSendingOutMails());

    TestTime::addHour();
    test()->assertTrue(test()->campaign->hasTroublesSendingOutMails());

    $send->update(['sent_at' => now()]);
    test()->assertFalse(test()->campaign->hasTroublesSendingOutMails());
});

it('can inline the styles of the html', function () {
    /** @var Campaign $campaign */
    $campaign = Campaign::factory()->create(['html' => '
        <html>
        <style>

            body {
                background-color: #e8eff6;
                }
        </style>
        <body>My body</body>
        </html>',
    ]);

    test()->assertMatchesHtmlSnapshotWithoutWhitespace($campaign->htmlWithInlinedCss());
});

it('doesnt change the doctype', function () {
    /** @var Campaign $campaign */
    $campaign = Campaign::factory()->create(['html' => '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html>
        <style>
            body {
                background-color: #e8eff6;
            }
        </style>
        <body>My body</body>
        </html>',
    ]);

    test()->assertEquals(
        '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
        explode("\n", $campaign->htmlWithInlinedCss())[0]
    );
});

it('can inline the styles of the html with custom mailable', function () {
    /** @var Campaign $campaign */
    $campaign = Campaign::factory()->create(['mailable_class' => TestMailcoachMailWithStaticHtml::class]);
    $campaign->content('');

    test()->assertMatchesHtmlSnapshotWithoutWhitespace($campaign->htmlWithInlinedCss());
});

it('can pull subject from custom mailable', function () {
    /** @var Campaign $campaign */
    $campaign = Campaign::factory()->create(['mailable_class' => TestMailcoachMail::class, 'subject' => 'This is the campaign subject and should be overwritten.']);
    $campaign->pullSubjectFromMailable();

    test()->assertEquals($campaign->subject, 'This is the subject from the custom mailable.');
});

it('can use a custom mailable with arguments', function () {
    $campaign = Campaign::factory()->create();

    $test_argument_value = 'This is a test value.';

    $campaign->useMailable(TestMailcoachMailWithArguments::class, ['test_argument' => $test_argument_value]);

    test()->assertEquals($test_argument_value, $campaign->contentFromMailable());
});

it('wont throw on unserializable segment class', function () {
    $campaign = Campaign::factory()->create([
        'segment_class' => EverySubscriberSegment::class,
    ]);

    $campaign->getSegment();

    test()->expectNotToPerformAssertions();
});

it('gets links', function () {
    $myHtml = '<html><a href="https://google.com">Test</a></html>';

    $campaign = Campaign::factory()->create([
        'track_clicks' => true,
        'html' => $myHtml,
    ]);

    $links = $campaign->htmlLinks();
    test()->assertEquals(1, $links->count());
    test()->assertEquals('https://google.com', $links->first());
});

it('gets links with ampersands', function () {
    $myHtml = '<html><a href="https://google.com?foo=true&bar=false">Test</a></html>';

    $campaign = Campaign::factory()->create([
        'track_clicks' => true,
        'html' => $myHtml,
    ]);

    $links = $campaign->htmlLinks();
    test()->assertEquals(1, $links->count());
    test()->assertEquals('https://google.com?foo=true&bar=false', $links->first());
});

// Helpers
function assertModels(array $expectedModels, Collection $actualModels)
{
    test()->assertEquals(count($expectedModels), $actualModels->count());
    test()->assertEquals(collect($expectedModels)->pluck('id')->toArray(), $actualModels->pluck('id')->toArray());
}
