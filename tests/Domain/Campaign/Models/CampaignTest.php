<?php

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Support\Segments\EverySubscriberSegment;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotSendCampaign;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignTestJob;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Shared\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestClasses\TestCustomInstanciatedQueryOnlyShouldSendToJohn;
use Spatie\Mailcoach\Tests\TestClasses\TestCustomQueryOnlyShouldSendToJohn;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMail;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMailWithArguments;
use Spatie\Mailcoach\Tests\TestClasses\TestMailcoachMailWithStaticHtml;

beforeEach(function () {
    test()->campaign = Campaign::create()->refresh();
});

test('the default status is draft', function () {
    expect(test()->campaign->status)->toEqual(CampaignStatus::Draft);
});

it('can set a from email', function () {
    test()->campaign->from('sender@example.com');

    expect(test()->campaign->from_email)->toEqual('sender@example.com');
});

it('can set both a from email and a from name', function () {
    test()->campaign->from('sender@example.com', 'Sender name');

    expect(test()->campaign->from_email)->toEqual('sender@example.com');
    expect(test()->campaign->from_name)->toEqual('Sender name');
});

it('can add a subject', function () {
    expect(test()->campaign->subject)->toBeNull();

    test()->campaign->subject('hello');

    expect(test()->campaign->refresh()->subject)->toEqual('hello');
});

it('can add a list', function () {
    $list = EmailList::factory()->create();

    test()->campaign->to($list);

    expect(test()->campaign->refresh()->email_list_id)->toEqual($list->id);
});

it('can be sent', function () {
    $list = EmailList::factory()->create();

    $campaign = Campaign::create()
        ->from('test@example.com')
        ->subject('test')
        ->content('my content')
        ->to($list)
        ->send();

    expect($campaign->status)->toEqual(CampaignStatus::Sending);
});

it('has a shorthand to set the list and send it in one go', function () {
    $list = EmailList::factory()->create();

    $campaign = Campaign::create()
        ->from('test@example.com')
        ->content('my content')
        ->subject('test')
        ->sendTo($list);

    expect($campaign->refresh()->email_list_id)->toEqual($list->id);
    expect($campaign->status)->toEqual(CampaignStatus::Sending);
});

test('a mailable can be set', function () {
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign $campaign */
    $campaign = Campaign::create()->useMailable(TestMailcoachMail::class);

    expect($campaign->mailable_class)->toEqual(TestMailcoachMail::class);
});

test('a mailable can set campaign html', function () {
    $campaign = (new CampaignFactory())->create(['html' => 'null']);

    $mailable = (new TestMailcoachMail())->setSendable($campaign);
    app()->instance(TestMailcoachMail::class, $mailable);

    $campaign->useMailable(TestMailcoachMail::class);
    $campaign->content($campaign->contentFromMailable());

    expect($campaign->html)->toEqual(app(TestMailcoachMail::class)->viewHtml);
});

it('will throw an exception when use an invalid mailable class', function () {
    test()->expectException(CouldNotSendCampaign::class);

    Campaign::create()->useMailable(static::class);
});

test('a segment can be set', function () {
    $campaign = Campaign::create()
        ->segment(TestCustomQueryOnlyShouldSendToJohn::class);

    expect($campaign->segment_class)->toEqual(serialize(TestCustomQueryOnlyShouldSendToJohn::class));
});

test('an instantiated segment class can be set', function () {
    $segment = new TestCustomInstanciatedQueryOnlyShouldSendToJohn('john@example.com');
    $campaign = Campaign::create()
        ->segment($segment);

    expect($campaign->segment_class)->toEqual(serialize($segment));
    expect($campaign->getSegment()->email)->toEqual('john@example.com');
});

it('will throw an exception when use an invalid segment class', function () {
    test()->expectException(CouldNotSendCampaign::class);

    Campaign::create()->segment(static::class);
});

test('html and content are not required when sending a mailable', function () {
    Bus::fake();

    $list = EmailList::factory()->create();

    $campaign = Campaign::create()
        ->from('test@example.com')
        ->content('my content')
        ->subject('test')
        ->sendTo($list);

    expect($campaign->status)->toEqual(CampaignStatus::Sending);
});

it('can use the default from email and name set on the email list', function () {
    Bus::fake();

    $list = EmailList::factory()->create([
        'default_from_email' => 'defaultEmailList@example.com',
        'default_from_name' => 'List name',
    ]);

    $campaign = Campaign::create()
        ->content('my content')
        ->subject('test')
        ->sendTo($list);

    expect($campaign->status)->toEqual(CampaignStatus::Sending);
    expect($campaign->from_email)->toEqual('defaultEmailList@example.com');
    expect($campaign->from_name)->toEqual('List name');
});

it('can use the default reply to email and name set on the email list', function () {
    Bus::fake();

    $list = EmailList::factory()->create([
        'default_reply_to_email' => 'defaultEmailList@example.com',
        'default_reply_to_name' => 'List name',
    ]);

    $campaign = Campaign::create()
        ->content('my content')
        ->subject('test')
        ->sendTo($list);

    expect($campaign->status)->toEqual(CampaignStatus::Sending);
    expect($campaign->reply_to_email)->toEqual('defaultEmailList@example.com');
    expect($campaign->reply_to_name)->toEqual('List name');
});

it('will prefer the email and from name from the campaign over the defaults set on the email list', function () {
    Bus::fake();

    $list = EmailList::factory()->create([
        'default_from_email' => 'defaultEmailList@example.com',
        'default_from_name' => 'List name',
    ]);

    $campaign = Campaign::create()
        ->content('my content')
        ->subject('test')
        ->from('campaign@example.com', 'campaign from name')
        ->sendTo($list);

    expect($campaign->status)->toEqual(CampaignStatus::Sending);
    expect($campaign->from_email)->toEqual('campaign@example.com');
    expect($campaign->from_name)->toEqual('campaign from name');
});

it('will prefer the email and reply to name from the campaign over the defaults set on the email list', function () {
    Bus::fake();

    $list = EmailList::factory()->create([
        'default_reply_to_email' => 'defaultEmailList@example.com',
        'default_reply_to_name' => 'List name',
    ]);

    $campaign = Campaign::create()
        ->content('my content')
        ->subject('test')
        ->from('campaign@example.com', 'campaign from name')
        ->replyTo('replyToCampaign@example.com', 'reply to from campaign')
        ->sendTo($list);

    expect($campaign->status)->toEqual(CampaignStatus::Sending);
    expect($campaign->reply_to_email)->toEqual('replyToCampaign@example.com');
    expect($campaign->reply_to_name)->toEqual('reply to from campaign');
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
        expect($job->campaign->id)->toEqual(test()->campaign->id);
        expect($job->email)->toEqual($email);

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

    test()->campaign->update(['status' => CampaignStatus::Sent]);

    test()->campaign->dispatchCalculateStatistics();

    Bus::assertDispatched(CalculateStatisticsJob::class, 1);
});

it('wont calculate statistics if it doesnt have any new sends', function () {
    test()->campaign->update(['status' => CampaignStatus::Sent]);
    test()->campaign->update(['statistics_calculated_at' => now()]);

    $queryCount = 0;
    DB::listen(function ($query) use (&$queryCount) {
        $queryCount++;
    });

    expect($queryCount)->toBe(0);

    test()->campaign->dispatchCalculateStatistics();

    expect($queryCount)->toBe(6); // 5 queries to get events + 1 to update statistics calculated at

    \Spatie\Mailcoach\Domain\Shared\Models\Send::factory()->create([
        'campaign_id' => test()->campaign->id,
    ]);

    test()->campaign->dispatchCalculateStatistics();

    expect($queryCount)->toBeGreaterThan(20); // A lot of queries to calculate the statistics
});

it('will only dispatch a calculate statistics job if it is sent', function () {
    Queue::fake();

    test()->campaign->update(['statistics_calculated_at' => now(), 'status' => CampaignStatus::Cancelled]);

    test()->campaign->dispatchCalculateStatistics();

    Queue::assertNotPushed(CalculateStatisticsJob::class);

    \Spatie\Mailcoach\Domain\Shared\Models\Send::factory()->create([
        'campaign_id' => test()->campaign->id,
    ]);

    test()->campaign->update(['status' => CampaignStatus::Sent]);

    test()->campaign->dispatchCalculateStatistics();

    Queue::assertPushed(CalculateStatisticsJob::class);
});

it('will not dispatch the recalculation job twice', function () {
    Bus::fake();

    test()->campaign->update(['status' => CampaignStatus::Sent]);

    test()->campaign->dispatchCalculateStatistics();
    test()->campaign->dispatchCalculateStatistics();

    Bus::assertDispatched(CalculateStatisticsJob::class, 1);
});

it('can dispatch the recalculation job again after the previous job has run', function () {
    Bus::fake();

    test()->campaign->update(['status' => CampaignStatus::Sent]);

    test()->campaign->dispatchCalculateStatistics();

    (new CalculateStatisticsJob(test()->campaign))->handle();

    \Spatie\Mailcoach\Domain\Shared\Models\Send::factory()->create([
        'campaign_id' => test()->campaign->id,
    ]);

    cache()->lock(
        'laravel_unique_job:'.CalculateStatisticsJob::class.test()->campaign->uuid
    )->forceRelease(); // Lock released

    test()->campaign->dispatchCalculateStatistics();

    Bus::assertDispatched(CalculateStatisticsJob::class, 2);
});

it('has scopes to get campaigns in various states', function () {
    Campaign::all()->each->delete();

    $draftCampaign = Campaign::factory()->create([
        'status' => CampaignStatus::Draft,
    ]);

    $scheduledInThePastCampaign = Campaign::factory()->create([
        'status' => CampaignStatus::Draft,
        'scheduled_at' => now()->subSecond(),
    ]);

    $scheduledNowCampaign = Campaign::factory()->create([
        'status' => CampaignStatus::Draft,
        'scheduled_at' => now(),
    ]);

    $scheduledInFutureCampaign = Campaign::factory()->create([
        'status' => CampaignStatus::Draft,
        'scheduled_at' => now()->addSecond(),
    ]);

    $sendingCampaign = Campaign::factory()->create([
        'status' => CampaignStatus::Sending,
    ]);

    $sentCampaign = Campaign::factory()->create([
        'status' => CampaignStatus::Sent,
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

    expect('This is the subject from the custom mailable.')->toEqual($campaign->subject);
});

it('can use a custom mailable with arguments', function () {
    $campaign = Campaign::factory()->create();

    $test_argument_value = 'This is a test value.';

    $campaign->useMailable(TestMailcoachMailWithArguments::class, ['test_argument' => $test_argument_value]);

    expect($campaign->contentFromMailable())->toEqual($test_argument_value);
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
        'html' => $myHtml,
    ]);

    $links = $campaign->htmlLinks();
    expect($links->count())->toEqual(1);
    expect($links->first())->toEqual('https://google.com');
});

it('ignores duplicates', function () {
    $myHtml = '<html><a href="https://google.com">Test</a><a href="https://google.com">Test</a></html>';

    $campaign = Campaign::factory()->create([
        'html' => $myHtml,
    ]);

    $links = $campaign->htmlLinks();
    expect($links->count())->toEqual(1);
});

it('ignores empty links', function () {
    $myHtml = '<html><a href="https://google.com">Test</a><a></a></html>';

    $campaign = Campaign::factory()->create([
        'html' => $myHtml,
    ]);

    $links = $campaign->htmlLinks();
    expect($links->count())->toEqual(1);
});

it('gets links with ampersands', function () {
    $myHtml = '<html><a href="https://google.com?foo=true&bar=false">Test</a></html>';

    $campaign = Campaign::factory()->create([
        'html' => $myHtml,
    ]);

    $links = $campaign->htmlLinks();
    expect($links->count())->toEqual(1);
    expect($links->first())->toEqual('https://google.com?foo=true&bar=false');
});

// Helpers
function assertModels(array $expectedModels, Collection $actualModels)
{
    expect($actualModels->count())->toEqual(count($expectedModels));
    expect($actualModels->pluck('id')->toArray())->toEqual(collect($expectedModels)->pluck('id')->toArray());
}
