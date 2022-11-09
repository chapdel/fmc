<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

beforeEach(function () {
    $this->withExceptionHandling();

    /** @var EmailList emailList */
    $this->emailList = EmailList::factory()->create([
        'website_slug' => 'this-is-the-slug',
        'allow_form_subscriptions' => true,
        'show_subscription_form_on_website' => true,
        'has_website' => true,
    ]);

    /** @var Campaign campaign */
    $this->campaign = Campaign::factory()->create([
        'subject' => 'This is the subject of the campaign',
        'email_list_id' => $this->emailList->id,
        'status' => CampaignStatus::Sent,
        'sent_at' => now(),
    ]);
});

it('it can generate the full url to the website', function (string $slug) {
    $this->emailList->update([
        'website_slug' => $slug,
    ]);

    expect($this->emailList->websiteUrl())->toEqual('http://localhost/mailcoach/archive/this-is-the-slug');
})->with([
    'this-is-the-slug',
    '/this-is-the-slug',
]);

it('will only display the website when it is enabled', function () {
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
    $this->campaign->delete();

    $this
        ->get($this->emailList->websiteUrl())
        ->assertSuccessful()
        ->assertSee('No campaigns have been sent');
});

it('will display sent campaigns on the list', function (CampaignStatus $status, bool $shouldBeDisplayed) {
    $assertionMethod = $shouldBeDisplayed
        ? 'assertSee'
        : 'assertDontSee';

    $this->campaign->update([
        'status' => $status,
    ]);

    $this
        ->get($this->emailList->websiteUrl())
        ->assertSuccessful()
        ->$assertionMethod($this->campaign->subject);
})->with([
    [CampaignStatus::Draft, false],
    [CampaignStatus::Sending, false],
    [CampaignStatus::Sent, true],
    [CampaignStatus::Cancelled, false],
]);

it('will not display a campaign on the list that should not be displayed', function () {
    $this
        ->get($this->emailList->websiteUrl())
        ->assertSee($this->campaign->subject);

    $this->campaign->update([
        'show_publicly' => false,
    ]);

    $this
        ->get($this->emailList->websiteUrl())
        ->assertDontSee($this->campaign->subject);
});

it('can generate the full url to a campaign page on the website', function () {
    expect($this->campaign->websiteUrl())
        ->toEqual("http://localhost/mailcoach/archive/this-is-the-slug/{$this->campaign->uuid}");
});

it('can display a campaign if the email list has a website', function () {
    $this->emailList->update([
        'has_website' => false,
    ]);

    $this
        ->get($this->campaign->websiteUrl())
        ->assertNotFound();

    $this->emailList->update([
        'has_website' => true,
    ]);

    $this
        ->get($this->campaign->websiteUrl())
        ->assertSuccessful();
});

it('will display the content of sent campaigns', function (CampaignStatus $status, bool $shouldBeDisplayed) {
    $assertionMethod = $shouldBeDisplayed
        ? 'assertSuccessful'
        : 'assertNotFound';

    $this->campaign->update([
        'status' => $status,
    ]);

    $this
        ->get($this->campaign->websiteUrl())
        ->$assertionMethod();
})->with([
    [CampaignStatus::Draft, false],
    [CampaignStatus::Sending, true],
    [CampaignStatus::Sent, true],
    [CampaignStatus::Cancelled, false],
]);

it('will not display a the content of campaign that should not be displayed', function () {
    $this
        ->get($this->campaign->websiteUrl())
        ->assertSuccessful();

    $this->campaign->update([
        'show_publicly' => false,
    ]);

    $this
        ->get($this->campaign->websiteUrl())
        ->assertNotFound();
});

it('can display a subscription form on the list campaigns page', function () {
    $this
        ->get($this->emailList->websiteUrl())
        ->assertSee('Subscribe');

    $this->emailList->update([
        'show_subscription_form_on_website' => false,
    ]);

    $this
        ->get($this->emailList->websiteUrl())
        ->assertDontSee('Subscribe');
});

it('can display a subscription form on the campaign detail page', function () {
    $this
        ->get($this->campaign->websiteUrl())
        ->assertSee('Subscribe');

    $this->emailList->update([
        'show_subscription_form_on_website' => false,
    ]);

    $this
        ->get($this->campaign->websiteUrl())
        ->assertDontSee('Subscribe');
});
