<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api;

use Spatie\Mailcoach\Http\Api\Controllers\TemplatesController;
use Spatie\Mailcoach\Models\Template;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class TemplatesControllerTest extends TestCase
{
    use RespondsToApiRequests;

    public function setUp(): void
    {
        parent::setUp();

        $this->loginToApi();
    }

    /** @test */
    public function it_can_list_all_templates()
    {
        $templates = Template::factory(3)->create();

        $this
            ->getJson(action([TemplatesController::class, 'index']))
            ->assertSuccessful()
            ->assertSeeText($templates->first()->name);
    }

    /** @test */
    public function it_can_search_templates()
    {
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
    }

    /** @test */
    public function the_api_can_show_a_template()
    {
        $template = Template::factory()->create();

        $this
            ->getJson(action([TemplatesController::class, 'show'], $template))
            ->assertSuccessful()
            ->assertJsonFragment(['name' => $template->name]);
    }

    /** @test */
    public function a_template_can_be_stored_using_the_api()
    {
        $attributes = [
            'name' => 'template name',
            'html' => 'template html',
        ];

        $this
            ->postJson(action([TemplatesController::class, 'store'], $attributes))
            ->assertSuccessful();

        $this->assertDatabaseHas('mailcoach_templates', $attributes);
    }

    /** @test */
    public function a_template_can_be_updated_using_the_api()
    {
        $template = Template::factory()->create();

        $attributes = [
            'name' => 'updated template name',
            'html' => 'updated template html',
        ];

        $this
            ->putJson(action([TemplatesController::class, 'update'], $template), $attributes)
            ->assertSuccessful();

        $template = $template->refresh();

        $this->assertEquals($attributes['name'], $template->name);
        $this->assertEquals($attributes['html'], $template->html);
    }

    /** @test */
    public function a_template_can_be_deleted_using_the_api()
    {
        $template = Template::factory()->create();

        $this
            ->deleteJson(action([TemplatesController::class, 'destroy'], $template))
            ->assertSuccessful();

        $this->assertCount(0, Template::get());
    }
}
