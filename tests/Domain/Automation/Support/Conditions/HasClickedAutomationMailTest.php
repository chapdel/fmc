<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasClickedAutomationMail;
use Spatie\Mailcoach\Domain\Content\Models\Click;
use Spatie\Mailcoach\Domain\Content\Models\Link;
use Spatie\Mailcoach\Domain\Shared\Actions\AddUtmTagsToUrlAction;

it('checks correctly that a user clicked an automation mail', function () {
    $automation = Automation::factory()->create();
    $subscriber = Subscriber::factory()->create();
    $automationMail = AutomationMail::factory()->create();

    $condition = new HasClickedAutomationMail($automation, $subscriber, [
        'automation_mail_id' => $automationMail->id,
        'automation_mail_link_url' => 'https://spatie.be',
    ]);

    expect($condition->check())->toBeFalse();

    $link = Link::factory()->create([
        'url' => 'https://spatie.be',
    ]);
    $click = Click::factory()->create([
        'link_id' => $link->id,
        'subscriber_id' => $subscriber->id,
    ]);
    $click->send->update(['content_item_id' => $automationMail->contentItem->id]);

    expect($condition->check())->toBeTrue();
});

it('checks correctly that a user clicked an automation mail with utm tags', function () {
    $automation = Automation::factory()->create();
    $subscriber = Subscriber::factory()->create();
    $automationMail = AutomationMail::factory()->create();
    $automationMail->contentItem->update([
        'utm_tags' => true,
    ]);

    $condition = new HasClickedAutomationMail($automation, $subscriber, [
        'automation_mail_id' => $automationMail->id,
        'automation_mail_link_url' => 'https://spatie.be',
    ]);

    expect($condition->check())->toBeFalse();

    $link = Link::factory()->create([
        'url' => app(AddUtmTagsToUrlAction::class)->execute('https://spatie.be', $automationMail->name),
        'content_item_id' => $automationMail->contentItem->id,
    ]);
    $click = Click::factory()->create([
        'link_id' => $link->id,
        'subscriber_id' => $subscriber->id,
    ]);
    $click->send->update([
        'content_item_id' => $automationMail->contentItem->id,
    ]);

    expect($condition->check())->toBeTrue();
});

it('returns false if a link is specified and its not the link', function () {
    $automation = Automation::factory()->create();
    $subscriber = Subscriber::factory()->create();
    $automationMail = AutomationMail::factory()->create();

    $condition = new HasClickedAutomationMail($automation, $subscriber, [
        'automation_mail_id' => $automationMail->id,
        'automation_mail_link_url' => 'https://example.com',
    ]);

    expect($condition->check())->toBeFalse();

    $link = Link::factory()->create([
        'url' => 'https://spatie.be',
    ]);
    $click = Click::factory()->create([
        'link_id' => $link->id,
        'subscriber_id' => $subscriber->id,
    ]);
    $click->send->update(['content_item_id' => $automationMail->contentItem->id]);

    expect($condition->check())->toBeFalse();
});

it('returns true if a link isnt specified and any link was clicked', function () {
    $automation = Automation::factory()->create();
    $subscriber = Subscriber::factory()->create();
    $automationMail = AutomationMail::factory()->create();

    $condition = new HasClickedAutomationMail($automation, $subscriber, [
        'automation_mail_id' => $automationMail->id,
    ]);

    expect($condition->check())->toBeFalse();

    $link = Link::factory()->create([
        'url' => 'https://spatie.be',
    ]);
    $click = Click::factory()->create([
        'link_id' => $link->id,
        'subscriber_id' => $subscriber->id,
    ]);
    $click->send->update(['content_item_id' => $automationMail->contentItem->id]);

    expect($condition->check())->toBeTrue();
});
