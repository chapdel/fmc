<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Actions;

use Illuminate\Support\Facades\Cache;
use Spatie\Mailcoach\Domain\Automation\Actions\CreateAutomationAction;
use Spatie\Mailcoach\Domain\Automation\Actions\UpdateAutomationMailAction;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\TestTime\TestTime;

class UpdateAutomationMailActionTest extends TestCase
{
    /** @test * */
    public function it_updates_an_automation_mail()
    {
        TestTime::freeze();

        $automationMail = AutomationMail::factory()->create();

        $action = resolve(UpdateAutomationMailAction::class);

        $action->execute($automationMail, [
            'name' => 'My Automation mail',
            'subject' => 'A subject',
            'html' => '<html></html>',
            'structured_html' => '',
            'track_opens' => false,
            'track_clicks' => false,
            'utm_tags' => false,
            'last_modified_at' => now()->startOfSecond(),
        ]);

        $this->assertEquals([
            'name' => 'My Automation mail',
            'subject' => 'A subject',
            'html' => '<html></html>',
            'structured_html' => '',
            'track_opens' => false,
            'track_clicks' => false,
            'utm_tags' => false,
            'last_modified_at' => now()->startOfSecond(),
        ], [
            'name' => $automationMail->name,
            'subject' => $automationMail->subject,
            'html' => $automationMail->html,
            'structured_html' => $automationMail->structured_html,
            'track_opens' => $automationMail->track_opens,
            'track_clicks' => $automationMail->track_clicks,
            'utm_tags' => $automationMail->utm_tags,
            'last_modified_at' => $automationMail->last_modified_at,
        ]);
    }
}
