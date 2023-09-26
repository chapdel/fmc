<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Template\Models\Template;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\MailcoachMarkdownEditor\Editor as MarkdownEditor;
use Spatie\Snapshots\MatchesSnapshots;

uses(RespondsToApiRequests::class);
uses(MatchesSnapshots::class);

beforeEach(function () {
    test()->loginToApi();
});

test('a campaign can be updated using the api', function () {
    $campaign = Campaign::factory()->create();

    $attributes = [
        'name' => 'name',
        'email_list_uuid' => EmailList::factory()->create()->uuid,
        'html' => 'html',
        'subject' => 'some subject',
        'schedule_at' => '2022-01-01 10:00:00',
    ];

    $this
        ->putJson(action([CampaignsController::class, 'update'], $campaign->uuid), $attributes)
        ->assertSuccessful();

    $campaign = $campaign->fresh();

    foreach ($attributes as $attributeName => $attributeValue) {
        if ($attributeName === 'schedule_at') {
            $attributeName = 'scheduled_at';
        }

        if ($attributeName === 'email_list_uuid') {
            expect($campaign->emailList->uuid)->toEqual($attributeValue);

            continue;
        }

        if (in_array($attributeName, ['html', 'subject'])) {
            expect($campaign->contentItem->$attributeName)->toEqual($attributeValue);

            continue;
        }

        expect($campaign->$attributeName)->toEqual($attributeValue);
    }
});

it('can accept the values for a template when using a markdown editor', function () {
    config()->set('mailcoach.content_editor', MarkdownEditor::class);

    $campaign = Campaign::factory()->create();

    $template = Template::create([
        'html' => $this->stub('TemplateHtml/default.html'),
        'contains_placeholders' => true,
        'name' => 'Default',
    ]);

    $attributes = [
        'name' => 'name',
        'email_list_uuid' => EmailList::factory()->create()->uuid,
        'fields' => [
            'title' => 'This is my title',
            'content' => '# This is some markdown',
        ],
        'template_uuid' => $template->uuid,
    ];

    $this
        ->putJson(action([CampaignsController::class, 'update'], $campaign->uuid), $attributes)
        ->assertSuccessful();

    $this->assertMatchesSnapshot($campaign->refresh()->structured_html);
    $this->assertMatchesSnapshot($campaign->refresh()->html);
});
