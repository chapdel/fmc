<?php

namespace Spatie\Mailcoach\Tests\Feature\Controllers\App;

use Spatie\Mailcoach\Http\App\Controllers\TemplatesController;
use Spatie\Mailcoach\Models\Template;
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
        $template = factory(Template::class)->create();

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
        $template = factory(Template::class)->create();

        $this
            ->delete(action([TemplatesController::class, 'destroy'], $template))
            ->assertRedirect(action([TemplatesController::class, 'index']));

        $this->assertCount(0, Template::get());
    }
}
