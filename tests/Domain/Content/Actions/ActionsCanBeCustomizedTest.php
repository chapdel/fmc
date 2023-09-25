<?php

use Illuminate\Support\Facades\Artisan;
use Spatie\Mailcoach\Domain\Campaign\Enums\CampaignStatus;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;
use Spatie\Mailcoach\Tests\TestClasses\CustomPersonalizeTextAction;
use Spatie\Mailcoach\Tests\TestClasses\CustomPrepareEmailHtmlAction;
use Spatie\Mailcoach\Tests\TestClasses\CustomPrepareWebviewHtmlAction;

test('the personalize html action can be customized', function () {
    config()->set('mailcoach.actions.personalize_text', CustomPersonalizeTextAction::class);

    $campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
        'status' => CampaignStatus::Draft,
    ]);

    $campaign->send();
    Artisan::call('mailcoach:send-scheduled-campaigns');
    Artisan::call('mailcoach:send-campaign-mails');

    expect($campaign->emailList->subscribers->first()->email)->toEqual('overridden@example.com');
});

test('the prepare email html action can be customized', function () {
    config()->set('mailcoach.actions.prepare_email_html', CustomPrepareEmailHtmlAction::class);

    $campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
        'status' => CampaignStatus::Draft,
    ]);

    $campaign->send();
    Artisan::call('mailcoach:send-scheduled-campaigns');

    expect($campaign->emailList->subscribers->first()->email)->toEqual('overridden@example.com');
});

test('the prepare webview html action can be customized', function () {
    config()->set('mailcoach.actions.prepare_webview_html', CustomPrepareWebviewHtmlAction::class);

    $campaign = (new CampaignFactory())->withSubscriberCount(1)->create([
        'status' => CampaignStatus::Draft,
    ]);

    $campaign->send();
    Artisan::call('mailcoach:send-scheduled-campaigns');

    expect($campaign->emailList->subscribers->first()->email)->toEqual('overridden@example.com');
});
