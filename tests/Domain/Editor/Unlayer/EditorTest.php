<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Template\Models\Template;

it('can render a view', function () {
    $template = Template::factory()->create();
    Livewire::test('mailcoach::editor-unlayer', ['model' => $template])
        ->assertSee('unlayer.createEditor');
});

test('passes along configured options', function () {
    config(['mailcoach.unlayer.options' => [
        'appearance' => ['theme' => 'dark'],
    ]]);

    $template = Template::factory()->create();
    Livewire::test('mailcoach::editor-unlayer', ['model' => $template])
        ->assertSee('appearance')
        ->assertSee('dark');
});
