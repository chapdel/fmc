<?php

namespace Spatie\Mailcoach\Tests\Models;

use Spatie\Mailcoach\Models\Campaign;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Tests\Factories\EmailListFactory;
use Spatie\Mailcoach\Tests\TestCase;

class UsesMailcoachModelsTest extends TestCase
{

    /** @var \Spatie\Mailcoach\Models\EmailList */
    private EmailList $email_list;

    public function setUp(): void
    {
        parent::setUp();

        $this->email_list = (new EmailListFactory())->create();
    }

    /** @test */
    public function the_configured_campaign_model_extends_package_model()
    {
        $this->assertEquals(is_a($this->email_list->getCampaignClass(), Campaign::class, true), true);
    }

    /** @test */
    public function the_configured_email_list_model_extends_package_model()
    {
        $this->assertEquals(is_a($this->email_list->getEmailListClass(), EmailList::class, true), true);
    }

    /** @test */
    public function the_configured_subscriber_model_extends_package_model()
    {
        $this->assertEquals(is_a($this->email_list->getSubscriberClass(), Subscriber::class, true), true);
    }
}
