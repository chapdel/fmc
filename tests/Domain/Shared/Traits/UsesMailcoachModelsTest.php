<?php

namespace Spatie\Mailcoach\Tests\Domain\Shared\Traits;

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Models\Campaign;
use Spatie\Mailcoach\Domain\Campaign\Models\Template;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\Factories\EmailListFactory;
use Spatie\Mailcoach\Tests\TestCase;

class UsesMailcoachModelsTest extends TestCase
{
    /** @var \Spatie\Mailcoach\Domain\Audience\Models\EmailList */
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

    /** @test */
    public function the_configured_template_model_extends_package_model()
    {
        $class = $this->email_list->getTemplateClass();
        $this->assertInstanceOf(Template::class, new $class);
    }

    /** @test */
    public function the_configured_send_model_extends_package_model()
    {
        $class = $this->email_list->getSendClass();
        $this->assertInstanceOf(Send::class, new $class);
    }
}
