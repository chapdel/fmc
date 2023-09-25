<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Content\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Actions\SendMailAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

beforeEach(function () {
    /** @var EmailList */
    $this->emailList = EmailList::factory()->create([
        'has_website' => true,
        'website_slug' => 'hey',
    ]);

    /** @var Campaign */
    $this->campaign = Campaign::factory()->create([
        'email_list_id' => $this->emailList->id,
    ]);

    $this->campaign->contentItem->update([
        'html' => '::websiteCampaignUrl::',
        'email_html' => '::websiteCampaignUrl::',
    ]);

    $this->send = Send::factory()->create([
        'content_item_id' => $this->campaign->contentItem->id,
    ]);
});

it('replaces the placeholder with the URL of the campaign on the website', function () {
    $mailerOriginal = Mail::getFacadeRoot();
    Mail::fake();

    app(SendMailAction::class)->execute($this->send);

    Mail::assertSent(function (MailcoachMail $mail) use ($mailerOriginal) {
        Mail::swap($mailerOriginal);

        $mail->assertSeeInHtml($this->campaign->websiteUrl());

        return true;
    });
});

it('will replace the placeholder with an empty string if the email list does not have a website', function () {
    $this->emailList->update([
        'has_website' => false,
    ]);

    $mailerOriginal = Mail::getFacadeRoot();
    Mail::fake();

    app(SendMailAction::class)->execute($this->send);

    Mail::assertSent(function (MailcoachMail $mail) use ($mailerOriginal) {
        Mail::swap($mailerOriginal);

        $mail->assertDontSeeInHtml($this->campaign->websiteUrl());

        return true;
    });
});
