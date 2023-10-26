<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Database\Factories\TemplateFactory;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Livewire\Editor\TextAreaEditorComponent;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;

it('can create a component', function () {
    $template = TemplateFactory::new()->create([
        'name' => 'My template',
        'html' => '<h1>My template</h1>',
    ]);

    Livewire::test(TextAreaEditorComponent::class, ['model' => $template])
        ->assertStatus(200)
        ->assertSet('model', $template)
        ->assertSet('templateId', null)
        ->assertSet('template', null)
        ->assertSet('templateFieldValues', ['html' => '<h1>My template</h1>'])
        ->assertSet('fullHtml', '<h1>My template</h1>')
        ->assertSet('emails', '')
        ->assertSet('quiet', false)
        ->assertSet('hasError', false);
});

it('can create a component with template', function () {
    test()->authenticate();

    $campaign = CampaignFactory::new()->create([
        'html' => null,
        'structured_html' => null,
    ]);

    $template = TemplateFactory::new()->create([
        'name' => 'My template',
        'html' => '<h1>My template</h1>',
    ]);

    $campaign->contentItem->template()->associate($template)->save();

    Livewire::test(TextAreaEditorComponent::class, ['model' => $campaign->contentItem])
        ->assertStatus(200)
        ->assertSet('model', $campaign->contentItem)
        ->assertSet('templateId', $template->id)
        ->assertSet('template', $template)
        ->assertSet('templateFieldValues', ['html' => '<h1>My template</h1>'])
        ->assertSet('fullHtml', '<h1>My template</h1>')
        ->assertSet('emails', '')
        ->assertSet('quiet', false)
        ->assertSet('hasError', false);
});

it('can create a component with an Mjml format template', function () {
    test()->authenticate();

    $campaign = CampaignFactory::new()->create([
        'html' => null,
        'structured_html' => null,
    ]);

    $template = TemplateFactory::new()->create([
        'name' => 'My template',
        'html' => '<mjml><mj-body><mj-section><mj-column><mj-text>HelloWorld</mj-text></mj-column></mj-section></mj-body></mjml>',
    ]);

    $campaign->contentItem->template()->associate($template)->save();

    $livewire = Livewire::test(TextAreaEditorComponent::class, ['model' => $campaign->contentItem])
        ->assertStatus(200)
        ->assertSet('model', $campaign->contentItem)
        ->assertSet('templateId', $template->id)
        ->assertSet('template', $template)
        ->assertSet('templateFieldValues', ['html' => '<mjml><mj-body><mj-section><mj-column><mj-text>HelloWorld</mj-text></mj-column></mj-section></mj-body></mjml>'])
        ->assertSet('emails', '')
        ->assertSet('quiet', false)
        ->assertSet('hasError', false);

    expect($livewire->fullHtml)->toMatchSnapshot();
});

it('can save a component with a template placeholder', function () {
    test()->authenticate();

    $campaign = CampaignFactory::new()->create([
        'html' => null,
        'structured_html' => null,
    ]);

    $template = TemplateFactory::new()->create([
        'name' => 'My template',
        'html' => '<html lang="en"><body><h1>[[[header]]]</h1></body></html>',
    ]);

    Livewire::test(TextAreaEditorComponent::class, ['model' => $campaign->contentItem])
        // Select the template
        ->set('templateId', $template->id)
        // Fill in content
        ->set('templateFieldValues.header', 'My Campaign Header')
        ->assertSet('fullHtml', '<html lang="en"><body><h1>My Campaign Header</h1></body></html>')
        // Save the campaign
        ->call('save')
        ->assertSet('hasError', false)
        ->assertSet('autosaveConflict', false)
        ->assertSet('fullHtml', '<html lang="en"><body><h1>My Campaign Header</h1></body></html>');

    $this->assertDatabaseHas(Campaign::getCampaignTableName(), [
        'id' => $campaign->id,
    ]);

    $this->assertDatabaseHas(Campaign::getContentItemTableName(), [
        'model_id' => $campaign->id,
        'template_id' => $template->id,
        'html' => '<html lang="en"><body><h1>My Campaign Header</h1></body></html>',
        'structured_html' => '{"templateValues":{"header":"My Campaign Header"}}',
    ]);
});

it('can save a component with mjml format template', function () {
    test()->authenticate();

    $campaign = CampaignFactory::new()->create([
        'html' => null,
        'structured_html' => null,
    ]);

    $template = TemplateFactory::new()->create([
        'name' => 'My template',
        'html' => '<mjml><mj-body><mj-section><mj-column><mj-text>[[[column1]]]</mj-text></mj-column></mj-section></mj-body></mjml>',
    ]);

    $livewire = Livewire::test(TextAreaEditorComponent::class, ['model' => $campaign->contentItem])
        ->set('templateId', $template->id)
        ->set('templateFieldValues.column1', 'My Campaign Header');

    expect($livewire->fullHtml)->toMatchSnapshot();
});

it('cannot explicitly save invalid mjml content with a template', function () {
    test()->authenticate();

    $campaign = CampaignFactory::new()->create([
        'html' => null,
        'structured_html' => null,
    ]);

    $template = TemplateFactory::new()->create([
        'name' => 'My template',
        'html' => '<mjml><mj-body><mj-section><mj-column><mj-text>[[[column1]]]</mj-text></mj-column></mj-section></mj-body></mjml>',
    ]);

    Livewire::test(TextAreaEditorComponent::class, ['model' => $campaign->contentItem])
        ->set('templateId', $template->id)
        ->set('templateFieldValues.column1', '<mjml><mjml>')
        ->call('save')
        ->assertNotDispatched('editorSavedQuietly')
        ->assertNotDispatched('editorSaved');
});

it('cannot explicitly save invalid mjml content on a template', function () {
    test()->authenticate();

    $template = TemplateFactory::new()->create([
        'name' => 'My template',
        'html' => '<mjml><m-body><mj-section><mj-column><mj-text>[[[column1]]]</mj-text></mj-column></mj-section></m-body></mjml>',
    ]);

    Livewire::test(TextAreaEditorComponent::class, ['model' => $template])
        ->call('save')
        ->assertNotDispatched('editorSavedQuietly')
        ->assertNotDispatched('editorSaved');
});
