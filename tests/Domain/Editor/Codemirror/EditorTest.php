<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Template\Models\Template;

it('can render a view', function () {
    $template = Template::factory()->create();
    Livewire::test('mailcoach::editor-markdown', ['model' => $template])
        ->assertSee('window.init');
});
