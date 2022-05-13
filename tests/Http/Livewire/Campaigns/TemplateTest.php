<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;

it('can update a template', function () {
    $this->authenticate();

    $template = Template::factory()->create();

    $attributes = [
        'name' => 'template name',
        'html' => 'template html',
    ];

    Livewire::test(\Spatie\Mailcoach\Http\App\Livewire\Campaigns\Template::class, ['template' => $template])
        ->set('template.name', 'template name')
        ->set('template.html', 'template html')
        ->call('save')
        ->assertHasNoErrors();

    $attributes['id'] = $template->id;

    test()->assertDatabaseHas(static::getTemplateTableName(), $attributes);
});
