<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Actions\PersonalizeTextAction;
use Spatie\Mailcoach\Domain\Content\Actions\PrepareEmailHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;

beforeEach(function () {
    /** @var EmailList */
    $this->emailList = EmailList::factory()->create([
        'has_website' => true,
        'website_slug' => 'hey',
    ]);

    /** @var Campaign */
    $this->campaign = CampaignFactory::new()->create([
        'email_list_id' => $this->emailList->id,
        'html' => '::websiteUrl::',
    ]);

    $this->send = Send::factory()
        ->create(['content_item_id' => $this->campaign->contentItem->id]);
});

it('replaces the placeholder with the URL of the website', function () {
    app(PrepareEmailHtmlAction::class)->execute($this->campaign);
    $result = app(PersonalizeTextAction::class)->execute($this->campaign->contentItem->email_html, $this->send);

    expect($result)->toContain($this->campaign->emailList->websiteUrl());
});

it('will replace the placeholder with an empty string if the email list does not have a website', function () {
    $this->emailList->update([
        'has_website' => false,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($this->campaign);
    $result = app(PersonalizeTextAction::class)->execute($this->campaign->email_html, $this->send);

    expect($result)
        ->not()->toContain(route('mailcoach.website', ltrim($this->emailList->website_slug, '/')))
        ->and($result)
        ->not()->toContain('::websiteUrl::');
});
