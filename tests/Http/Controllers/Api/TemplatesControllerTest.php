<?php

use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Http\Api\Controllers\TemplatesController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();
});

it('can list all templates', function () {
    $templates = Template::factory(3)->create();

    $this
        ->getJson(action([TemplatesController::class, 'index']))
        ->assertSuccessful()
        ->assertSeeText($templates->first()->name);
});

it('can search templates', function () {
    Template::factory()->create([
        'name' => 'one',
    ]);

    Template::factory()->create([
        'name' => 'two',
    ]);

    $this
        ->getJson(action([TemplatesController::class, 'index']) . '?filter[search]=two')
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['name' => 'two']);
});

test('the api can show a template', function () {
    $template = Template::factory()->create();

    $this
        ->getJson(action([TemplatesController::class, 'show'], $template))
        ->assertSuccessful()
        ->assertJsonFragment(['name' => $template->name]);
});

test('a template can be stored using the api', function () {
    $attributes = [
        'name' => 'template name',
        'html' => 'template html',
    ];

    $this
        ->postJson(action([TemplatesController::class, 'store'], $attributes))
        ->assertSuccessful();

    test()->assertDatabaseHas(static::getTemplateTableName(), $attributes);
});

test('a template can be updated using the api', function () {
    $template = Template::factory()->create();

    $attributes = [
        'name' => 'updated template name',
        'html' => 'updated template html',
    ];

    $this
        ->putJson(action([TemplatesController::class, 'update'], $template), $attributes)
        ->assertSuccessful();

    $template = $template->refresh();

    expect($template->name)->toEqual($attributes['name']);
    expect($template->html)->toEqual($attributes['html']);
});

test('a template can be deleted using the api', function () {
    $template = Template::factory()->create();

    $this
        ->deleteJson(action([TemplatesController::class, 'destroy'], $template))
        ->assertSuccessful();

    expect(Template::get())->toHaveCount(0);
});
