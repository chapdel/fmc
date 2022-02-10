<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Spatie\Mailcoach\Tests\Factories\CampaignFactory;

beforeEach(function () {
    test()->campaign = (new CampaignFactory())
        ->withSubscriberCount(3)
        ->create();

    test()->campaign->emailList->update(['campaign_mailer' => 'some-mailer']);

    Mail::fake();

    Event::fake();
});
