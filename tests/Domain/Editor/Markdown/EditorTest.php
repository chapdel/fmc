<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;

it('can render a view', function () {
    $contentItem = ContentItem::factory()->create();

    Livewire::test('mailcoach::editor-markdown', ['model' => $contentItem])
        ->assertSee('window.init');
});
