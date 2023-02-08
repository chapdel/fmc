<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\TemplateComponent;

it('can save a template', function () {
    $this->authenticate();

    Livewire::test(TemplateComponent::class, ['template' => Template::factory()->create(['name' => 'old'])])
        ->set('template.name', 'new')
        ->call('save');

    $this->assertDatabaseHas(static::getTemplateTableName(), ['name' => 'new']);
});

it('re-renders emails using the template', function () {
    $this->authenticate();

    $template = Template::factory()->create([
        'name' => 'Template',
        'html' => '<h1>[[[title:text]]]</h1><p>A new addition</p>',
    ]);

    $campaign = Campaign::factory()->create([
        'template_id' => $template->id,
        'status' => CampaignStatus::Draft,
        'html' => '<h1>Title</h1>',
        'structured_html' => json_encode(['templateValues' => ['title' => 'Title']]),
    ]);

    Livewire::test(TemplateComponent::class, ['template' => $template])
        ->call('save');

    expect($campaign->fresh()->html)->toBe('<h1>Title</h1><p>A new addition</p>');
});
