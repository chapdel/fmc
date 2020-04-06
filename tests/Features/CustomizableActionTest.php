<?php

namespace Spatie\Mailcoach\Tests\Features;

use Illuminate\Foundation\Auth\User;
use Spatie\Mailcoach\Enums\CampaignStatus;
use Spatie\Mailcoach\Exceptions\InvalidConfig;
use Spatie\Mailcoach\Jobs\ImportSubscribersJob;
use Spatie\Mailcoach\Jobs\SendCampaignJob;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Models\SubscriberImport;
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
        config()->set('mailcoach.actions.personalize_html', CustomPersonalizeHtmlAction::class);

        $campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
            'status' => CampaignStatus::DRAFT,
        ]);

        dispatch(new SendCampaignJob($campaign));

        $this->assertEquals('overridden@example.com', $campaign->emailList->subscribers->first()->email);
    }

    /** @test */
    public function the_personalize_subject_action_can_be_customized()
    {
        config()->set('mailcoach.actions.personalize_subject', CustomPersonalizeSubjectAction::class);

        $campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
            'status' => CampaignStatus::DRAFT,
        ]);

        dispatch(new SendCampaignJob($campaign));

        $this->assertEquals('overridden@example.com', $campaign->emailList->subscribers->first()->email);
    }

    /** @test */
    public function the_prepare_email_html_action_can_be_customized()
    {
        config()->set('mailcoach.actions.prepare_email_html', CustomPrepareEmailHtmlAction::class);

        $campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
            'status' => CampaignStatus::DRAFT,
        ]);

        dispatch(new SendCampaignJob($campaign));

        $this->assertEquals('overridden@example.com', $campaign->emailList->subscribers->first()->email);
    }

    /** @test */
    public function the_prepare_subject_action_can_be_customized()
    {
        config()->set('mailcoach.actions.prepare_subject', CustomPrepareSubjectAction::class);

        $campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
            'status' => CampaignStatus::DRAFT,
        ]);

        dispatch(new SendCampaignJob($campaign));

        $this->assertEquals('overridden@example.com', $campaign->emailList->subscribers->first()->email);
    }

    /** @test */
    public function the_prepare_webview_html_action_can_be_customized()
    {
        config()->set('mailcoach.actions.prepare_webview_html', CustomPrepareWebviewHtmlAction::class);

        $campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
            'status' => CampaignStatus::DRAFT,
        ]);

        dispatch(new SendCampaignJob($campaign));

        $this->assertEquals('overridden@example.com', $campaign->emailList->subscribers->first()->email);
    }

    /** @test */
    public function the_create_subscriber_action_can_be_customized()
    {
        config()->set('mailcoach.actions.create_subscriber', CustomCreateSubscriberAction::class);

        /** @var \Spatie\Mailcoach\Models\EmailList $emailList */
        $emailList = factory(EmailList::class)->create();

        $subscriber = $emailList->subscribe('john@example.com');

        $this->assertEquals('overridden@example.com', $subscriber->email);
    }

    /** @test */
    public function the_confirm_subscription_class_can_be_customized()
    {
        config()->set('mailcoach.actions.confirm_subscriber', CustomConfirmSubscriberAction::class);

        $emailList = factory(EmailList::class)->create([
            'requires_confirmation' => true,
    ]);

        $subscriber = Subscriber::createWithEmail('john@example.com')->subscribeTo($emailList);

        $subscriber->confirm();

        $this->assertEquals('overridden@example.com', $subscriber->email);
    }

    /** @test */
    public function a_wrongly_configured_class_will_result_in_an_exception()
    {
        config()->set('mailcoach.actions.create_subscriber', 'invalid-action');

        /** @var \Spatie\Mailcoach\Models\EmailList $emailList */
        $emailList = factory(EmailList::class)->create();

        $this->expectException(InvalidConfig::class);

        $emailList->subscribe('john@example.com');
    }

    /** @test */
    public function the_import_subscribers_class_can_be_customized()
    {
        config()->set('mailcoach.actions.import_subscribers', CustomImportSubscribersAction::class);

        $subscriberImport = factory(SubscriberImport::class)->create();

        $user = factory(User::class)->create();

        $this->expectExceptionMessage('Inside custom import action');

        dispatch(new ImportSubscribersJob($subscriberImport, $user));
    }
}
