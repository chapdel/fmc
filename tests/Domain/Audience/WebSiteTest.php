<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

beforeEach(function () {
    /** @var EmailList emailList */
    $this->emailList = EmailList::factory()->create([
        'website_slug' => 'this-is-the-slug',
        'has_website' => true,
    ]);
});

it('it can generate the full url to the website', function () {
    expect($this->emailList->websiteUrl())->toEqual('http://localhost/mailcoach/this-is-the-slug');
});

it('it will only the website when it is enabled', function () {
    $this->emailList->update([
        'has_website' => false,
    ]);

    $this
        ->get($this->emailList->websiteUrl())
        ->assertRedirect();

    $this->emailList->update([
        'has_website' => true,
    ]);

    $this
        ->get($this->emailList->websiteUrl())
        ->assertSuccessful();
});

it('will display the title and intro', function () {
    $title = 'This is my title';
    $intro = 'This is my intro';

    $this->emailList->update([
        'website_title' => $title,
        'website_intro' => $intro,
    ]);

    $this
        ->get($this->emailList->websiteUrl())
        ->assertSuccessful()
        ->assertSee($title)
        ->assertSee($intro);
});

it('will display the rss feed when enabled', function () {
    $rssType = 'application/atom+xml';

    $this
        ->get($this->emailList->websiteUrl())
        ->assertSuccessful()
        ->assertDontSee($rssType);

    $this->emailList->update([
        'campaigns_feed_enabled' => true,
    ]);

    $this
        ->get($this->emailList->websiteUrl())
        ->assertSuccessful()
        ->assertSee($rssType);
});

it('will display a message when no campaigns have been sent yet', function () {
    $this
        ->get($this->emailList->websiteUrl())
        ->assertSuccessful()
        ->assertSee('No campaigns have been sent');
});

it('will display sent campaigns', function (CampaignStatus $status, bool $shouldBeDisplayed) {
    $assertionMethod = $shouldBeDisplayed
        ? 'assertSee'
        : 'assertDontSee';

    $campaign = Campaign::factory()->create([
        'subject' => 'This is the subject of the campaign',
        'email_list_id' => $this->emailList->id,
        'status' => $status,
    ]);

    $this
        ->get($this->emailList->websiteUrl())
        ->assertSuccessful()
        ->$assertionMethod($campaign->subject);
})->with([
    [CampaignStatus::Draft, false],
    [CampaignStatus::Sending, true],
    [CampaignStatus::Sent, true],
    [CampaignStatus::Cancelled, false],
]);
