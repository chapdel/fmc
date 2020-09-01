<?php

namespace Spatie\Mailcoach\Tests\Mails;

use Spatie\Mailcoach\Mails\ImportSubscribersResultMail;
use Spatie\Mailcoach\Models\SubscriberImport;
use Spatie\Mailcoach\Tests\TestCase;

class ImportSubscribersResultMailTest extends TestCase
{
    /** @test */
    public function it_can_render_the_import_subscribers_result_mail()
    {
        $subscriberImport = SubscriberImport::factory()->create();

        $this->assertIsString((new ImportSubscribersResultMail($subscriberImport))->render());
    }
}
