<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Http\App\Livewire\Campaigns\TemplatesComponent;

it('can delete a template', function () {
    $this->authenticate();

    $template = Template::factory()->create();

    Livewire::test(TemplatesComponent::class)
        ->call('deleteTemplate', $template->id);

    expect(Template::count())->toBe(0);
});

it('can duplicate a template', function () {
    $this->authenticate();

    $template = Template::factory()->create();

    Livewire::test(TemplatesComponent::class)
        ->call('duplicateTemplate', $template->id)
        ->assertRedirect(route('mailcoach.templates.edit', Template::orderByDesc('id')->first()));

    expect(Template::get())->toHaveCount(2);

    $templates = Template::get();

    $originalTemplate = $templates[0];
    $duplicateTemplate = $templates[1];

    expect($duplicateTemplate->name)->toEqual("Duplicate of {$originalTemplate->name}");
    expect($originalTemplate->html)->toEqual($duplicateTemplate->html);
    expect($originalTemplate->structured_html)->toEqual($duplicateTemplate->structured_html);
});
