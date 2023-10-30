<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Template\Models\Template;

it('can render a view', function () {
    $template = Template::factory()->create();
    Livewire::test('mailcoach::editor-editorjs', ['model' => $template])
        ->assertSee('window.init');
});
