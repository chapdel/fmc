<?php

use Spatie\Mailcoach\Domain\Campaign\Actions\PrepareEmailHtmlAction;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\CouldNotSendCampaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;
use function Spatie\Snapshots\assertMatchesHtmlSnapshot;


it('throws on invalid html', function () {
    $myHtml = '<h1>Hello<html><p>Hello world</p>';

    $campaign = Campaign::factory()->create([
        'track_clicks' => true,
        'html' => $myHtml,
    ]);

    test()->expectException(CouldNotSendCampaign::class);

    app(PrepareEmailHtmlAction::class)->execute($campaign);

    $campaign->refresh();
});

it('will automatically add html tags', function () {
    $myHtml = '<h1>Hello</h1><p>Hello world</p>';

    $campaign = Campaign::factory()->create([
        'track_clicks' => true,
        'html' => $myHtml,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($campaign);

    $campaign->refresh();

    assertMatchesHtmlSnapshot($campaign->email_html);
});

it('works with ampersands', function () {
    $myHtml = '<html><a href="https://google.com?foo=true&bar=false">Test</a></html>';

    $campaign = Campaign::factory()->create([
        'track_clicks' => true,
        'html' => $myHtml,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($campaign);

    $campaign->refresh();

    assertMatchesHtmlSnapshot($campaign->email_html);
});

it('will not double encode ampersands', function () {
    $myHtml = '<html>-></html>';

    $campaign = Campaign::factory()->create([
        'utm_tags' => true,
        'html' => $myHtml,
        'name' => 'test',
    ]);

    app(PrepareEmailHtmlAction::class)->execute($campaign);

    $campaign->refresh();

    expect($campaign->email_html)->toContain('-&gt;');
});

it('will not add html tags if they are already present', function () {
    $myHtml = '<html><head></head><body><h1>Hello</h1><p>Hello world</p></body></html>';

    $campaign = Campaign::factory()->create([
        'track_clicks' => true,
        'html' => $myHtml,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($campaign);

    $campaign->refresh();

    assertMatchesHtmlSnapshot($campaign->email_html);
});

it('will not add html tags before the doctype', function () {
    $myHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd"><h1>Hello</h1><p>Hello world</p>';

    $campaign = Campaign::factory()->create([
        'track_clicks' => true,
        'html' => $myHtml,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($campaign);

    $campaign->refresh();

    assertMatchesHtmlSnapshot($campaign->email_html);
});

it('will not change the doctype', function () {
    $myHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><h1>Hello</h1><p>Hello world</p>';

    $campaign = Campaign::factory()->create([
        'track_clicks' => true,
        'html' => $myHtml,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($campaign);

    $campaign->refresh();

    assertMatchesHtmlSnapshot($campaign->email_html);
});

it('will add utm tags', function () {
    $myHtml = '<html><body><h1>Hello</h1><a href="https://spatie.be">Hello world</a></body></html>';

    $campaign = Campaign::factory()->create([
        'track_clicks' => true,
        'html' => $myHtml,
        'utm_tags' => true,
        'name' => 'My Campaign',
    ]);

    app(PrepareEmailHtmlAction::class)->execute($campaign);

    $campaign->refresh();

    expect($campaign->email_html)->toContain(htmlspecialchars("https://spatie.be?utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign"));
    assertMatchesHtmlSnapshot($campaign->email_html);
});

it('will add utm tags to links that already have query parameters', function () {
    $myHtml = '<html><body><h1>Hello</h1><a href="https://spatie.be?foo=bar">Hello world</a></body></html>';

    $campaign = Campaign::factory()->create([
        'track_clicks' => true,
        'html' => $myHtml,
        'utm_tags' => true,
        'name' => 'My Campaign',
    ]);

    app(PrepareEmailHtmlAction::class)->execute($campaign);

    $campaign->refresh();

    expect($campaign->email_html)->toContain(htmlspecialchars("https://spatie.be?foo=bar&utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign"));
    assertMatchesHtmlSnapshot($campaign->email_html);
});

it('will add utm tags to urls with paths correctly', function () {
    $myHtml = '<html><body><h1>Hello</h1><a href="https://freek.dev/1234-my-blogpost">Hello world</a></body></html>';

    $campaign = Campaign::factory()->create([
        'track_clicks' => true,
        'html' => $myHtml,
        'utm_tags' => true,
        'name' => 'My Campaign',
    ]);

    app(PrepareEmailHtmlAction::class)->execute($campaign);

    $campaign->refresh();

    expect($campaign->email_html)->toContain(htmlspecialchars("https://freek.dev/1234-my-blogpost?utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign"));
    assertMatchesHtmlSnapshot($campaign->email_html);
});

it('will add utm tags to urls with paths correctly when the link is added twice', function () {
    $myHtml = '<html><body><h1>Hello</h1><a href="https://freek.dev">Hello world</a><a href="https://freek.dev/1234-my-blogpost">Hello world</a></body></html>';

    $campaign = Campaign::factory()->create([
        'track_clicks' => true,
        'html' => $myHtml,
        'utm_tags' => true,
        'name' => 'My Campaign',
    ]);

    app(PrepareEmailHtmlAction::class)->execute($campaign);

    $campaign->refresh();

    expect($campaign->email_html)->toContain(htmlspecialchars("https://freek.dev?utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign"));
    expect($campaign->email_html)->toContain(htmlspecialchars("https://freek.dev/1234-my-blogpost?utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign"));
    assertMatchesHtmlSnapshot($campaign->email_html);
});
