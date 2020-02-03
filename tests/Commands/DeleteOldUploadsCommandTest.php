<?php

namespace Spatie\Mailcoach\Tests\Commands;

use Spatie\Mailcoach\Commands\DeleteOldUploadsCommand;
use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\Template;
use Spatie\Mailcoach\Models\Upload;
use Spatie\Mailcoach\Tests\TestCase;

class DeleteOldUploadsCommandTest extends TestCase
{
    private Campaign $campaign;
    private Template $template;

    public function setUp(): void
    {
        parent::setUp();

        $this->campaign = factory(Campaign::class)->create();
        $this->template = factory(Template::class)->create();
    }

    /** @test */
    public function it_will_delete_all_uploads_that_have_no_campaign_or_template()
    {
        $upload = factory(Upload::class)->create();

        $upload->templates()->attach($this->template);

        $this->assertEquals(1, Upload::count());
        $this->artisan(DeleteOldUploadsCommand::class)->assertExitCode(0);
        $this->assertEquals(1, Upload::count());

        $upload->templates()->detach($this->template);

        $this->assertEquals(1, Upload::count());
        $this->artisan(DeleteOldUploadsCommand::class)->assertExitCode(0);
        $this->assertEquals(0, Upload::count());
    }

    /** @test * */
    public function it_will_not_delete_if_it_is_attached_to_either_templates_or_campaigns()
    {
        $upload = factory(Upload::class)->create();

        $upload->templates()->attach($this->template);
        $upload->campaigns()->attach($this->campaign);

        $this->assertEquals(1, Upload::count());
        $this->artisan(DeleteOldUploadsCommand::class)->assertExitCode(0);
        $this->assertEquals(1, Upload::count());

        $upload->templates()->detach($this->template);

        $this->assertEquals(1, Upload::count());
        $this->artisan(DeleteOldUploadsCommand::class)->assertExitCode(0);
        $this->assertEquals(1, Upload::count());

        $upload->campaigns()->detach($this->campaign);

        $this->assertEquals(1, Upload::count());
        $this->artisan(DeleteOldUploadsCommand::class)->assertExitCode(0);
        $this->assertEquals(0, Upload::count());
    }
}
