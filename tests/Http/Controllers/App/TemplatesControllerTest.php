<?php

use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\TemplatesController;

beforeEach(function () {
    test()->authenticate();
});

it('can create a template', function () {
    $attributes = [
        'name' => 'template name',
        'html' => 'template html',
    ];

    $this
        ->post(action([TemplatesController::class, 'store']), $attributes)
        ->assertSessionHasNoErrors();

    test()->assertDatabaseHas(static::getTemplateTableName(), $attributes);
});

it('can update a template', function () {
    $template = Template::factory()->create();

    $attributes = [
        'name' => 'template name',
        'html' => 'template html',
    ];

    $this
        ->put(action([TemplatesController::class, 'update'], $template->id), $attributes)
        ->assertSessionHasNoErrors();

    $attributes['id'] = $template->id;

    test()->assertDatabaseHas(static::getTemplateTableName(), $attributes);
});

it('can delete a template', function () {
    $template = Template::factory()->create();

    $this
        ->delete(action([TemplatesController::class, 'destroy'], $template))
        ->assertRedirect(action([TemplatesController::class, 'index']));

    expect(Template::get())->toHaveCount(0);
});

it('can duplicate a template', function () {
    $template = Template::factory()->create();

    $this
        ->post(action([TemplatesController::class, 'duplicate'], $template))
        ->assertRedirect(route('mailcoach.templates.edit', Template::orderByDesc('id')->first()))
        ->assertSessionHasNoErrors();

    expect(Template::get())->toHaveCount(2);

    $templates = Template::get();

    $originalTemplate = $templates[0];
    $duplicateTemplate = $templates[1];

    expect($duplicateTemplate->name)->toEqual("Duplicate of {$originalTemplate->name}");
    expect($originalTemplate->html)->toEqual($duplicateTemplate->html);
    expect($originalTemplate->structured_html)->toEqual($duplicateTemplate->structured_html);
});
