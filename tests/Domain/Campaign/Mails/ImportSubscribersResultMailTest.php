<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Mails;

use Spatie\Mailcoach\Domain\Campaign\Mails\ImportSubscribersResultMail;
use Spatie\Mailcoach\Domain\Campaign\Models\SubscriberImport;
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
