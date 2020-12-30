<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Actions;

use Spatie\Mailcoach\Database\Factories\UserFactory;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Domain\Campaign\Exceptions\InvalidConfig;
use Spatie\Mailcoach\Domain\Campaign\Jobs\ImportSubscribersJob;
use Spatie\Mailcoach\Domain\Campaign\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Campaign\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Models\SubscriberImport;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Mailcoach\Tests\TestClasses\CustomConfirmSubscriberAction;
use Spatie\Mailcoach\Tests\TestClasses\CustomCreateSubscriberAction;
use Spatie\Mailcoach\Tests\TestClasses\CustomImportSubscribersAction;
use Spatie\Mailcoach\Tests\TestClasses\CustomPersonalizeHtmlAction;
use Spatie\Mailcoach\Tests\TestClasses\CustomPersonalizeSubjectAction;
use Spatie\Mailcoach\Tests\TestClasses\CustomPrepareEmailHtmlAction;
use Spatie\Mailcoach\Tests\TestClasses\CustomPrepareSubjectAction;
use Spatie\Mailcoach\Tests\TestClasses\CustomPrepareWebviewHtmlAction;

class CustomizableActionTest extends TestCase
{
    /** @test */
    public function the_personalize_html_action_can_be_customized()
    {
        config()->set('mailcoach.campaigns.actions.personalize_html', CustomPersonalizeHtmlAction::class);

        $campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
            'status' => CampaignStatus::DRAFT,
        ]);

        dispatch(new SendCampaignJob($campaign));

        $this->assertEquals('overridden@example.com', $campaign->emailList->subscribers->first()->email);
    }

    /** @test */
    public function the_personalize_subject_action_can_be_customized()
    {
        config()->set('mailcoach.campaigns.actions.personalize_subject', CustomPersonalizeSubjectAction::class);

        $campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
            'status' => CampaignStatus::DRAFT,
        ]);

        dispatch(new SendCampaignJob($campaign));

        $this->assertEquals('overridden@example.com', $campaign->emailList->subscribers->first()->email);
    }

    /** @test */
    public function the_prepare_email_html_action_can_be_customized()
    {
        config()->set('mailcoach.campaigns.actions.prepare_email_html', CustomPrepareEmailHtmlAction::class);

        $campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
            'status' => CampaignStatus::DRAFT,
        ]);

        dispatch(new SendCampaignJob($campaign));

        $this->assertEquals('overridden@example.com', $campaign->emailList->subscribers->first()->email);
    }

    /** @test */
    public function the_prepare_subject_action_can_be_customized()
    {
        config()->set('mailcoach.campaigns.actions.prepare_subject', CustomPrepareSubjectAction::class);

        $campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
            'status' => CampaignStatus::DRAFT,
        ]);

        dispatch(new SendCampaignJob($campaign));

        $this->assertEquals('overridden@example.com', $campaign->emailList->subscribers->first()->email);
    }

    /** @test */
    public function the_prepare_webview_html_action_can_be_customized()
    {
        config()->set('mailcoach.campaigns.actions.prepare_webview_html', CustomPrepareWebviewHtmlAction::class);

        $campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
            'status' => CampaignStatus::DRAFT,
        ]);

        dispatch(new SendCampaignJob($campaign));

        $this->assertEquals('overridden@example.com', $campaign->emailList->subscribers->first()->email);
    }

    /** @test */
    public function the_create_subscriber_action_can_be_customized()
    {
        config()->set('mailcoach.campaigns.actions.create_subscriber', CustomCreateSubscriberAction::class);

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\EmailList $emailList */
        $emailList = EmailList::factory()->create();

        $subscriber = $emailList->subscribe('john@example.com');

        $this->assertEquals('overridden@example.com', $subscriber->email);
    }

    /** @test */
    public function the_confirm_subscription_class_can_be_customized()
    {
        config()->set('mailcoach.campaigns.actions.confirm_subscriber', CustomConfirmSubscriberAction::class);

        $emailList = EmailList::factory()->create([
            'requires_confirmation' => true,
    ]);

        $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);

        $subscriber->confirm();

        $this->assertEquals('overridden@example.com', $subscriber->email);
    }

    /** @test */
    public function a_wrongly_configured_class_will_result_in_an_exception()
    {
        config()->set('mailcoach.campaigns.actions.create_subscriber', 'invalid-action');

        /** @var \Spatie\Mailcoach\Domain\Campaign\Models\EmailList $emailList */
        $emailList = EmailList::factory()->create();

        $this->expectException(InvalidConfig::class);

        $emailList->subscribe('john@example.com');
    }

    /** @test */
    public function the_import_subscribers_class_can_be_customized()
    {
        config()->set('mailcoach.campaigns.actions.import_subscribers', CustomImportSubscribersAction::class);

        $subscriberImport = SubscriberImport::factory()->create();

        $user = UserFactory::new()->create();

        $this->expectExceptionMessage('Inside custom import action');

        dispatch(new ImportSubscribersJob($subscriberImport, $user));
    }
}
