<?php

use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\TemplatesController;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

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

    test()->assertCount(0, Template::get());
});

it('can duplicate a template', function () {
    $template = Template::factory()->create();

    $this
        ->post(action([TemplatesController::class, 'duplicate'], $template))
        ->assertRedirect(route('mailcoach.templates.edit', Template::orderByDesc('id')->first()))
        ->assertSessionHasNoErrors();

    test()->assertCount(2, Template::get());

    $templates = Template::get();

    $originalTemplate = $templates[0];
    $duplicateTemplate = $templates[1];

    test()->assertEquals("Duplicate of {$originalTemplate->name}", $duplicateTemplate->name);
    test()->assertEquals($duplicateTemplate->html, $originalTemplate->html);
    test()->assertEquals($duplicateTemplate->structured_html, $originalTemplate->structured_html);
});
