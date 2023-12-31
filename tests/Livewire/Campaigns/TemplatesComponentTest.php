<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Template\Models\Template;
use Spatie\Mailcoach\Livewire\Templates\TemplatesComponent;

it('can duplicate a template', function () {
    $this->authenticate();

    $template = Template::factory()->create();

    Livewire::test(TemplatesComponent::class)
        ->call('duplicateTemplate', $template)
        ->assertRedirect(route('mailcoach.templates.edit', Template::orderByDesc('id')->first()));

    expect(Template::get())->toHaveCount(2);

    $templates = Template::get();

    $originalTemplate = $templates[0];
    $duplicateTemplate = $templates[1];

    expect($duplicateTemplate->name)->toEqual("{$originalTemplate->name} - copy");
    expect($originalTemplate->html)->toEqual($duplicateTemplate->html);
    expect($originalTemplate->structured_html)->toEqual($duplicateTemplate->structured_html);
});
