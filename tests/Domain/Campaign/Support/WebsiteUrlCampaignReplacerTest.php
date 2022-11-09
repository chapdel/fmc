<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Actions\PrepareEmailHtmlAction;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;

beforeEach(function () {
    /** @var EmailList */
    $this->emailList = EmailList::factory()->create([
        'has_website' => true,
        'website_slug' => 'hey',
    ]);

    /** @var Campaign */
    $this->campaign = Campaign::factory()->create([
        'email_list_id' => $this->emailList->id,
        'html' => '::websiteUrl::',
    ]);
});

it('replaces the placeholder with the URL of the website', function () {
    app(PrepareEmailHtmlAction::class)->execute($this->campaign);

    expect($this->campaign->refresh()->email_html)->toContain($this->campaign->emailList->websiteUrl());
});

it('will replace the placeholder with an empty string if the email list does not have a website', function () {
    $this->emailList->update([
        'has_website' => false,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($this->campaign);

    expect($this->campaign->refresh()->email_html)
        ->not()->toContain($this->campaign->emailList->websiteUrl())
        ->and($this->campaign->refresh()->email_html)
        ->not()->toContain('::websiteUrl::');
});
