<?php

use Spatie\Mailcoach\Livewire\Templates\CreateTemplateComponent;

it('can create a template', function () {
    $this->authenticate();

    \Livewire\Livewire::test(CreateTemplateComponent::class)
        ->set('name', 'template name')
        ->call('saveTemplate');

    test()->assertDatabaseHas(static::getTemplateTableName(), ['name' => 'template name']);
});
