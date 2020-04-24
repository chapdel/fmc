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
        $class = $this->email_list->getCampaignClass();
        $this->assertInstanceOf(Campaign::class, new $class);
    }

    /** @test */
    public function the_configured_email_list_model_extends_package_model()
    {
        $class = $this->email_list->getEmailListClass();
        $this->assertInstanceOf(EmailList::class, new $class);
    }

    /** @test */
    public function the_configured_subscriber_model_extends_package_model()
    {
        $class = $this->email_list->getSubscriberClass();
        $this->assertInstanceOf(Subscriber::class, new $class);
    }
}
