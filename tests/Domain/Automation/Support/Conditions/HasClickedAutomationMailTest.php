<?php

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailClick;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailLink;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasClickedAutomationMail;
use Spatie\Mailcoach\Domain\Shared\Actions\AddUtmTagsToUrlAction;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('checks correctly that a user clicked an automation mail', function () {
    $automation = Automation::factory()->create();
    $subscriber = Subscriber::factory()->create();
    $automationMail = AutomationMail::factory()->create();

    $condition = new HasClickedAutomationMail($automation, $subscriber, [
        'automation_mail_id' => $automationMail->id,
        'automation_mail_link_url' => 'https://spatie.be',
    ]);

    test()->assertFalse($condition->check());

    $link = AutomationMailLink::factory()->create([
        'url' => 'https://spatie.be',
    ]);
    $click = AutomationMailClick::factory()->create([
        'automation_mail_link_id' => $link->id,
        'subscriber_id' => $subscriber->id,
    ]);
    $click->send->update(['automation_mail_id' => $automationMail->id]);

    test()->assertTrue($condition->check());
});

it('checks correctly that a user clicked an automation mail with utm tags', function () {
    $automation = Automation::factory()->create();
    $subscriber = Subscriber::factory()->create();
    $automationMail = AutomationMail::factory()->create([
        'utm_tags' => true,
    ]);

    $condition = new HasClickedAutomationMail($automation, $subscriber, [
        'automation_mail_id' => $automationMail->id,
        'automation_mail_link_url' => 'https://spatie.be',
    ]);

    test()->assertFalse($condition->check());

    $link = AutomationMailLink::factory()->create([
        'url' => app(AddUtmTagsToUrlAction::class)->execute('https://spatie.be', $automationMail->name),
    ]);
    $click = AutomationMailClick::factory()->create([
        'automation_mail_link_id' => $link->id,
        'subscriber_id' => $subscriber->id,
    ]);
    $click->send->update(['automation_mail_id' => $automationMail->id]);

    test()->assertTrue($condition->check());
});

it('returns false if a link is specified and its not the link', function () {
    $automation = Automation::factory()->create();
    $subscriber = Subscriber::factory()->create();
    $automationMail = AutomationMail::factory()->create();

    $condition = new HasClickedAutomationMail($automation, $subscriber, [
        'automation_mail_id' => $automationMail->id,
        'automation_mail_link_url' => 'https://example.com',
    ]);

    test()->assertFalse($condition->check());

    $link = AutomationMailLink::factory()->create([
        'url' => 'https://spatie.be',
    ]);
    $click = AutomationMailClick::factory()->create([
        'automation_mail_link_id' => $link->id,
        'subscriber_id' => $subscriber->id,
    ]);
    $click->send->update(['automation_mail_id' => $automationMail->id]);

    test()->assertFalse($condition->check());
});

it('returns true if a link isnt specified and any link was clicked', function () {
    $automation = Automation::factory()->create();
    $subscriber = Subscriber::factory()->create();
    $automationMail = AutomationMail::factory()->create();

    $condition = new HasClickedAutomationMail($automation, $subscriber, [
        'automation_mail_id' => $automationMail->id,
    ]);

    test()->assertFalse($condition->check());

    $link = AutomationMailLink::factory()->create([
        'url' => 'https://spatie.be',
    ]);
    $click = AutomationMailClick::factory()->create([
        'automation_mail_link_id' => $link->id,
        'subscriber_id' => $subscriber->id,
    ]);
    $click->send->update(['automation_mail_id' => $automationMail->id]);

    test()->assertTrue($condition->check());
});
