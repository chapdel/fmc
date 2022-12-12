<?php

use Illuminate\Support\Arr;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Campaign\Policies\CampaignPolicy;
use Spatie\Mailcoach\Http\Api\Controllers\Campaigns\CampaignsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestClasses\CustomCampaignDenyAllPolicy;
use Spatie\MailcoachMarkdownEditor\Editor as MarkdownEditor;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();

    test()->postAttributes = getPostAttributes();
});

test('a campaign can be created using the api', function () {
    $this
        ->postJson(action([CampaignsController::class, 'store']), test()->postAttributes)
        ->assertSuccessful();

    $campaign = Campaign::first();

    foreach (Arr::except(test()->postAttributes, ['type', 'email_list_uuid']) as $attributeName => $attributeValue) {
        test()->assertEquals($attributeValue, $campaign->$attributeName);
    }
    test()->assertEquals(test()->postAttributes['email_list_uuid'], $campaign->emailList->uuid);
});

it('can be created with a tagsegment', function () {
    $tagsegment = TagSegment::factory()->create();

    $this
        ->postJson(action([CampaignsController::class, 'store']), array_merge(test()->postAttributes, [
            'segment_uuid' => $tagsegment->uuid,
        ]))
        ->assertSuccessful();

    $campaign = Campaign::first();

    test()->assertEquals(TagSegment::class, $campaign->segment_class);
});

test('access is denied by custom authorization policy', function () {
    app()->bind(CampaignPolicy::class, CustomCampaignDenyAllPolicy::class);

    $this
        ->withExceptionHandling()
        ->postJson(action([CampaignsController::class, 'store']), test()->postAttributes)
        ->assertForbidden();
});

it('can accept the values for a template when creating a campaign and using a markdown editor', function () {
    config()->set('mailcoach.content_editor', MarkdownEditor::class);

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

    $response = $this
        ->postJson(action([CampaignsController::class, 'store']), $attributes);

    $response->assertSuccessful();

    $campaign = Campaign::first();

    $this->assertMatchesSnapshot($campaign->refresh()->structured_html);
    $this->assertMatchesSnapshot($campaign->refresh()->html);

    expect($response->json('data.fields.title'))->toBe('This is my title');
    expect($response->json('data.fields.content'))->toBe('# This is some markdown');
});

it('will not fail when fields are missing when creating a campaign', function () {
    config()->set('mailcoach.content_editor', MarkdownEditor::class);

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
            // 'content' => '# This is some markdown',  // provide no content
        ],
        'template_uuid' => $template->uuid,
    ];

    $response = $this
        ->postJson(action([CampaignsController::class, 'store']), $attributes);

    $response->assertSuccessful();

    $campaign = Campaign::first();

    $this->assertMatchesSnapshot($campaign->refresh()->structured_html);
    $this->assertMatchesSnapshot($campaign->refresh()->html);

    expect($response->json('data.fields.title'))->toBe('This is my title');
    expect($response->json('data.fields.content'))->toBe('');
});


function getPostAttributes(): array
{
    return [
        'name' => 'name',
        'subject' => 'subject',
        'type' => CampaignStatus::Draft,
        'email_list_uuid' => EmailList::factory()->create()->uuid,
        'html' => 'html',
    ];
}
