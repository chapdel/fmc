<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api;

use Spatie\Mailcoach\Http\Api\Controllers\TemplatesController;
use Spatie\Mailcoach\Models\Template;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\UsesApi;
use Spatie\Mailcoach\Tests\TestCase;

class TemplatesControllerTest extends TestCase
{
    use UsesApi;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();
    }

    /** @test */
    public function it_can_list_all_templates()
    {
        $templates = factory(Template::class, 3)->create();

        $this
            ->get(action([TemplatesController::class, 'index']))
            ->assertSuccessful()
            ->assertSeeText($templates->first()->name);
    }

    /** @test */
    public function it_can_search_templates()
    {
        factory(Template::class)->create([
            'name' => 'one',
        ]);

        factory(Template::class)->create([
            'name' => 'two',
        ]);

        $this
            ->get(action([TemplatesController::class, 'index']) . '?filter[search]=test')
            ->assertSuccessful()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'two'])
            ->assertDontSee('one');
    }

    /** @test */
    public function a_template_can_be_stored_using_the_api()
    {
        $attributes = [
            'name' => 'template name',
            'html' => 'template html',
        ];

        $this
            ->post(action([TemplatesController::class, 'store'], $attributes))
            ->assertSuccessful();

        $this->assertDatabaseHas('mailcoach_templates', $attributes);
    }
}
