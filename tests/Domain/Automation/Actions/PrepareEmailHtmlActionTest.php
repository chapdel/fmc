<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Actions;

use Spatie\Mailcoach\Domain\Automation\Actions\PrepareEmailHtmlAction;
use Spatie\Mailcoach\Domain\Automation\Exceptions\CouldNotSendAutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class PrepareEmailHtmlActionTest extends TestCase
{
    use MatchesSnapshots;

    /** @test * */
    public function it_throws_on_invalid_html()
    {
        $myHtml = '<h1>Hello<html><p>Hello world</p>';

        $campaign = AutomationMail::factory()->create([
            'track_clicks' => true,
            'html' => $myHtml,
        ]);

        $this->expectException(CouldNotSendAutomationMail::class);

        app(PrepareEmailHtmlAction::class)->execute($campaign);

        $campaign->refresh();
    }

    /** @test */
    public function it_will_automatically_add_html_tags()
    {
        $myHtml = '<h1>Hello</h1><p>Hello world</p>';

        $campaign = AutomationMail::factory()->create([
            'track_clicks' => true,
            'html' => $myHtml,
        ]);

        app(PrepareEmailHtmlAction::class)->execute($campaign);

        $campaign->refresh();

        $this->assertMatchesHtmlSnapshot($campaign->email_html);
    }

    /** @test */
    public function it_works_with_ampersands()
    {
        $myHtml = '<html><a href="https://google.com?foo=true&bar=false">Test</a></html>';

        $campaign = AutomationMail::factory()->create([
            'track_clicks' => true,
            'html' => $myHtml,
        ]);

        app(PrepareEmailHtmlAction::class)->execute($campaign);

        $campaign->refresh();

        $this->assertMatchesHtmlSnapshot($campaign->email_html);
    }

    /** @test */
    public function it_will_not_double_encode_ampersands()
    {
        $myHtml = '<html>-></html>';

        $campaign = AutomationMail::factory()->create([
            'html' => $myHtml,
            'utm_tags' => true,
        ]);

        app(PrepareEmailHtmlAction::class)->execute($campaign);

        $campaign->refresh();

        $this->assertStringContainsString('-&gt;', $campaign->email_html);
    }

    /** @test */
    public function it_will_not_add_html_tags_if_they_are_already_present()
    {
        $myHtml = '<html><head></head><body><h1>Hello</h1><p>Hello world</p></body></html>';

        $campaign = AutomationMail::factory()->create([
            'track_clicks' => true,
            'html' => $myHtml,
        ]);

        app(PrepareEmailHtmlAction::class)->execute($campaign);

        $campaign->refresh();

        $this->assertMatchesHtmlSnapshot($campaign->email_html);
    }

    /** @test */
    public function it_will_not_add_html_tags_before_the_doctype()
    {
        $myHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd"><h1>Hello</h1><p>Hello world</p>';

        $campaign = AutomationMail::factory()->create([
            'track_clicks' => true,
            'html' => $myHtml,
        ]);

        app(PrepareEmailHtmlAction::class)->execute($campaign);

        $campaign->refresh();

        $this->assertMatchesHtmlSnapshot($campaign->email_html);
    }

    /** @test * */
    public function it_will_not_change_the_doctype()
    {
        $myHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><h1>Hello</h1><p>Hello world</p>';

        $campaign = AutomationMail::factory()->create([
            'track_clicks' => true,
            'html' => $myHtml,
        ]);

        app(PrepareEmailHtmlAction::class)->execute($campaign);

        $campaign->refresh();

        $this->assertMatchesHtmlSnapshot($campaign->email_html);
    }

    /** @test * */
    public function it_will_add_utm_tags()
    {
        $myHtml = '<html><body><h1>Hello</h1><a href="https://spatie.be">Hello world</a></body></html>';

        $campaign = AutomationMail::factory()->create([
            'track_clicks' => true,
            'html' => $myHtml,
            'utm_tags' => true,
            'name' => 'My AutomationMail',
        ]);

        app(PrepareEmailHtmlAction::class)->execute($campaign);

        $campaign->refresh();

        $this->assertStringContainsString(htmlspecialchars("https://spatie.be?utm_source=newsletter&utm_medium=email&utm_campaign=My+AutomationMail"), $campaign->email_html);
        $this->assertMatchesHtmlSnapshot($campaign->email_html);
    }

    /** @test * */
    public function it_will_add_utm_tags_to_links_that_already_have_query_parameters()
    {
        $myHtml = '<html><body><h1>Hello</h1><a href="https://spatie.be?foo=bar">Hello world</a></body></html>';

        $campaign = AutomationMail::factory()->create([
            'track_clicks' => true,
            'html' => $myHtml,
            'utm_tags' => true,
            'name' => 'My AutomationMail',
        ]);

        app(PrepareEmailHtmlAction::class)->execute($campaign);

        $campaign->refresh();

        $this->assertStringContainsString(htmlspecialchars("https://spatie.be?foo=bar&utm_source=newsletter&utm_medium=email&utm_campaign=My+AutomationMail"), $campaign->email_html);
        $this->assertMatchesHtmlSnapshot($campaign->email_html);
    }

    /** @test * */
    public function it_will_add_utm_tags_to_urls_with_paths_correctly()
    {
        $myHtml = '<html><body><h1>Hello</h1><a href="https://freek.dev/1234-my-blogpost">Hello world</a></body></html>';

        $campaign = AutomationMail::factory()->create([
            'track_clicks' => true,
            'html' => $myHtml,
            'utm_tags' => true,
            'name' => 'My AutomationMail',
        ]);

        app(PrepareEmailHtmlAction::class)->execute($campaign);

        $campaign->refresh();

        $this->assertStringContainsString(htmlspecialchars("https://freek.dev/1234-my-blogpost?utm_source=newsletter&utm_medium=email&utm_campaign=My+AutomationMail"), $campaign->email_html);
        $this->assertMatchesHtmlSnapshot($campaign->email_html);
    }

    /** @test * */
    public function it_will_add_utm_tags_to_urls_with_paths_correctly_when_the_link_is_added_twice()
    {
        $myHtml = '<html><body><h1>Hello</h1><a href="https://freek.dev">Hello world</a><a href="https://freek.dev/1234-my-blogpost">Hello world</a></body></html>';

        $automationMail = AutomationMail::factory()->create([
            'track_clicks' => true,
            'html' => $myHtml,
            'utm_tags' => true,
            'name' => 'My AutomationMail',
        ]);

        app(PrepareEmailHtmlAction::class)->execute($automationMail);

        $automationMail->refresh();

        $this->assertStringContainsString(htmlspecialchars("https://freek.dev?utm_source=newsletter&utm_medium=email&utm_campaign=My+AutomationMail"), $automationMail->email_html);
        $this->assertStringContainsString(htmlspecialchars("https://freek.dev/1234-my-blogpost?utm_source=newsletter&utm_medium=email&utm_campaign=My+AutomationMail"), $automationMail->email_html);
        $this->assertMatchesHtmlSnapshot($automationMail->email_html);
    }

    /** @test * */
    public function it_will_not_change_img_source()
    {
        $myHtml = '<html><body><h1>Hello</h1><a href="https://freek.dev">Hello world</a><img src="https://freek.dev"/></body></html>';

        $automationMail = AutomationMail::factory()->create([
            'track_clicks' => true,
            'html' => $myHtml,
            'utm_tags' => true,
            'name' => 'My AutomationMail',
        ]);

        app(PrepareEmailHtmlAction::class)->execute($automationMail);

        $automationMail->refresh();

        $this->assertStringContainsString('<img src="https://freek.dev">', $automationMail->email_html);
        $this->assertMatchesHtmlSnapshot($automationMail->email_html);
    }
}
