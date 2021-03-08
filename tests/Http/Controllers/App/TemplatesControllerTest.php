<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\App;

use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Http\App\Controllers\Campaigns\TemplatesController;
use Spatie\Mailcoach\Tests\TestCase;

class TemplatesControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->authenticate();
    }

    /** @test */
    public function it_can_create_a_template()
    {
        $attributes = [
            'name' => 'template name',
            'html' => 'template html',
        ];

        $this
            ->post(action([TemplatesController::class, 'store']), $attributes)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('mailcoach_templates', $attributes);
    }

    /** @test */
    public function it_can_update_a_template()
    {
        $template = Template::factory()->create();

        $attributes = [
            'name' => 'template name',
            'html' => 'template html',
        ];

        $this
            ->put(action([TemplatesController::class, 'update'], $template->id), $attributes)
            ->assertSessionHasNoErrors();

        $attributes['id'] = $template->id;

        $this->assertDatabaseHas('mailcoach_templates', $attributes);
    }

    /** @test */
    public function it_can_delete_a_template()
    {
        $template = Template::factory()->create();

        $this
            ->delete(action([TemplatesController::class, 'destroy'], $template))
            ->assertRedirect(action([TemplatesController::class, 'index']));

        $this->assertCount(0, Template::get());
    }

    /** @test */
    public function it_can_duplicate_a_template()
    {
        $template = Template::factory()->create();

        $this
            ->post(action([TemplatesController::class, 'duplicate'], $template))
            ->assertRedirect(route('mailcoach.templates.edit', Template::orderByDesc('id')->first()))
            ->assertSessionHasNoErrors();

        $this->assertCount(2, Template::get());

        $templates = Template::get();

        $originalTemplate = $templates[0];
        $duplicateTemplate = $templates[1];

        $this->assertEquals("Duplicate of {$originalTemplate->name}", $duplicateTemplate->name);
        $this->assertEquals($duplicateTemplate->html, $originalTemplate->html);
        $this->assertEquals($duplicateTemplate->structured_html, $originalTemplate->structured_html);
    }
}
