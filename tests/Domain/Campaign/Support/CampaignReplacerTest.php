<?php

use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Domain\Content\Actions\PersonalizeTextAction;
use Spatie\Mailcoach\Domain\Content\Actions\PrepareEmailHtmlAction;
use Spatie\Mailcoach\Domain\Content\Mails\MailcoachMail;
use Spatie\Mailcoach\Domain\Shared\Actions\SendMailAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;

test('campaign name should replaced in subject', function () {
    Mail::fake();

    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign */
    $campaign = (new CampaignFactory())->create([
        'name' => 'campaign1234',
        'subject' => '::campaign.name::',
    ]);

    $send = Send::factory()->create(['content_item_id' => $campaign->contentItem->id]);

    app(SendMailAction::class)->execute($send);

    Mail::assertSent(function (MailcoachMail $mail) {
        expect($mail->subject)->toBe('campaign1234');

        return true;
    });
});

test('campaign name should be replaced in subject with twig', function () {
    Mail::fake();

    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign */
    $campaign = (new CampaignFactory())->create([
        'name' => 'My campaign name',
        'subject' => '{{ campaign.name }}',
    ]);

    $send = Send::factory()->create(['content_item_id' => $campaign->contentItem->id]);

    app(SendMailAction::class)->execute($send);

    Mail::assertSent(function (MailcoachMail $mail) {
        expect($mail->subject)->toBe('My campaign name');

        return true;
    });
});

test('campaign name should replaced in email html', function () {
    $campaignName = 'test1234';

    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign */
    $campaign = (new CampaignFactory())->create([
        'name' => $campaignName,
        'html' => '::campaign.name::',
    ]);

    $send = Send::factory()->create([
        'content_item_id' => $campaign->contentItem->id,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($campaign);
    $result = app(PersonalizeTextAction::class)->execute($campaign->contentItem->email_html, $send);
    test()->assertMatchesHtmlSnapshot($result);
});

test('campaign name should replaced in email html with twig', function () {
    $campaignName = 'test1234';

    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign */
    $campaign = (new CampaignFactory())->create([
        'name' => $campaignName,
        'html' => '{{ campaign.name }}',
    ]);

    $send = Send::factory()->create([
        'content_item_id' => $campaign->contentItem->id,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($campaign);
    $result = app(PersonalizeTextAction::class)->execute($campaign->contentItem->email_html, $send);
    test()->assertMatchesHtmlSnapshot($result);
});

test('campaign name should replace in url encoded html', function () {
    $campaignName = 'test1234';

    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Campaign */
    $campaign = (new CampaignFactory())->create([
        'name' => $campaignName,
        'html' => urlencode('::campaign.name::'),
    ]);

    $send = Send::factory()->create([
        'content_item_id' => $campaign->contentItem->id,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($campaign);
    $result = app(PersonalizeTextAction::class)->execute($campaign->contentItem->email_html, $send);
    $campaign->refresh();
    test()->assertMatchesHtmlSnapshot($result);
});

test('campaign name should replace in raw url encoded html with twig', function () {
    $campaign = (new CampaignFactory())->create([
        'name' => 'test1234',
        'html' => rawurlencode('{{ campaign.name }}'),
    ]);

    $send = Send::factory()->create([
        'content_item_id' => $campaign->contentItem->id,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($campaign);

    $result = app(PersonalizeTextAction::class)->execute($campaign->contentItem->email_html, $send);
    $this->assertStringContainsString('test1234', $result);
});

test('campaign name should replace in url encoded html with twig', function () {
    $campaign = (new CampaignFactory())->create([
        'name' => 'test1234',
        'html' => urlencode('{{ campaign.name }}'),
    ]);

    $send = Send::factory()->create([
        'content_item_id' => $campaign->contentItem->id,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($campaign);

    $result = app(PersonalizeTextAction::class)->execute($campaign->contentItem->email_html, $send);
    $this->assertStringContainsString('test1234', $result);
});
