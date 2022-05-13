<?php

use Spatie\Mailcoach\Http\App\Livewire\Campaigns\CreateTemplate;

it('can create a template', function () {
    $this->authenticate();

    \Livewire\Livewire::test(CreateTemplate::class)
        ->set('name', 'template name')
        ->call('saveTemplate');

    test()->assertDatabaseHas(static::getTemplateTableName(), ['name' => 'template name']);
});
