<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Database\Factories\TemplateFactory;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Livewire\Editor\TextAreaEditorComponent;

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
        ->assertSet('hasError', false)
        ->assertSet('lastSavedAt', $template->updated_at);
});

it('can create a component with template', function () {
    test()->authenticate();

    $campaign = Campaign::factory()->create();

    $template = TemplateFactory::new()->create([
        'name' => 'My template',
        'html' => '<h1>My template</h1>',
    ]);

    $campaign->template()->associate($template)->save();

    Livewire::test(TextAreaEditorComponent::class, ['model' => $campaign])
        ->assertStatus(200)
        ->assertSet('model', $campaign)
        ->assertSet('templateId', $template->id)
        ->assertSet('template', $template)
        ->assertSet('templateFieldValues', ['html' => '<h1>My template</h1>'])
        ->assertSet('fullHtml', '<h1>My template</h1>')
        ->assertSet('emails', '')
        ->assertSet('quiet', false)
        ->assertSet('hasError', false)
        ->assertSet('lastSavedAt', $template->updated_at);
});

it('can create a component with an Mgml format template', function () {
    test()->authenticate();

    $campaign = Campaign::factory()->create();

    $template = TemplateFactory::new()->create([
        'name' => 'My template',
        'html' => '<mjml><mj-body><mj-section><mj-column><mj-text>HelloWorld</mj-text></mj-column></mj-section></mj-body></mjml>',
    ]);

    $campaign->template()->associate($template)->save();

    $livewire = Livewire::test(TextAreaEditorComponent::class, ['model' => $campaign])
        ->assertStatus(200)
        ->assertSet('model', $campaign)
        ->assertSet('templateId', $template->id)
        ->assertSet('template', $template)
        ->assertSet('templateFieldValues', ['html' => '<mjml><mj-body><mj-section><mj-column><mj-text>HelloWorld</mj-text></mj-column></mj-section></mj-body></mjml>'])
        ->assertSet('emails', '')
        ->assertSet('quiet', false)
        ->assertSet('hasError', false)
        ->assertSet('lastSavedAt', $template->updated_at);

    expect($livewire->fullHtml)->toMatchSnapshot();
});

it('can save a component with a template placeholder', function () {
    test()->authenticate();

    $campaign = Campaign::factory()->make([
        'html' => $html = 'My Campaign Header',
        'structured_html' => json_encode([
            'templateValues' => [
                'html' => $html,
                'header' => $html, // @todo how does this work in the editor?
            ],
        ], JSON_THROW_ON_ERROR),
        'template_id' => null,
    ]);

    $campaign->save();

    $template = TemplateFactory::new()->create([
        'name' => 'My template',
        'html' => '<html lang="en"><body><h1>[[[header]]]</h1></body></html>',
    ]);

    Livewire::test(TextAreaEditorComponent::class, ['model' => $campaign])
        ->assertSet('fullHtml', 'My Campaign Header')
        ->set('templateId', $template->id)

        ->call('save')
        ->assertSet('lastSavedAt', $campaign->fresh()->updated_at)
        ->assertSet('hasError', false)
        ->assertSet('autosaveConflict', false)
        ->assertSet('fullHtml', '<html lang="en"><body><h1>My Campaign Header</h1></body></html>');

    $this->assertDatabaseHas(Campaign::getCampaignTableName(), [
        'id' => $campaign->id,
        'template_id' => $template->id,
    ]);
});

it('can save a component with mgml format template', function () {

});
