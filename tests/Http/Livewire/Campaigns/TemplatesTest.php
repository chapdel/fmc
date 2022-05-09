<?php

use Spatie\Mailcoach\Domain\Campaign\Models\Template;

it('can delete a template', function () {
    $this->authenticate();

    $template = Template::factory()->create();

    \Livewire\Livewire::test(\Spatie\Mailcoach\Http\App\Livewire\Campaigns\Templates::class)
        ->call('deleteTemplate', $template->id);

    expect(Template::count())->toBe(0);
});
