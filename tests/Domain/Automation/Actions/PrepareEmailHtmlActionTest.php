<?php

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Content\Actions\PrepareEmailHtmlAction;

use function Spatie\Snapshots\assertMatchesHtmlSnapshot;

it('will automatically add html tags', function () {
    $myHtml = '<h1>Hello</h1><p>Hello world</p>';

    $sendable = AutomationMail::factory()->create();
    $sendable->contentItem->update([
        'html' => $myHtml,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($sendable);

    $sendable->refresh();

    assertMatchesHtmlSnapshot($sendable->contentItem->email_html);
});

it('minifies the html but keeps comments', function () {
    $myHtml = "<html ''  ><h1>Hello</h1>\n\n\n<p>Hello world</p><!--[if mso]><span>Hello</span><![endif]-->    </html>";

    $sendable = AutomationMail::factory()->create();
    $sendable->contentItem->update([
        'html' => $myHtml,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($sendable);

    $sendable->refresh();

    expect($sendable->contentItem->email_html)->toBe('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd"><html><body><h1>Hello</h1><p>Hello world</p><!--[if mso]><span>Hello</span><![endif]--></body></html>');
});

it('works with ampersands', function () {
    $myHtml = '<html><a href="https://google.com?foo=true&bar=false">Test</a></html>';

    $sendable = AutomationMail::factory()->create();
    $sendable->contentItem->update([
        'html' => $myHtml,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($sendable);

    $sendable->refresh();

    assertMatchesHtmlSnapshot($sendable->contentItem->email_html);
});

it('will not double encode ampersands', function () {
    $myHtml = '<html>-></html>';

    $sendable = AutomationMail::factory()->create();
    $sendable->contentItem->update([
        'html' => $myHtml,
        'utm_tags' => true,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($sendable);

    $sendable->refresh();

    expect($sendable->contentItem->email_html)->toContain('-&gt;');
});

it('will not add html tags if they are already present', function () {
    $myHtml = '<html><head></head><body><h1>Hello</h1><p>Hello world</p></body></html>';

    $sendable = AutomationMail::factory()->create();
    $sendable->contentItem->update([
        'html' => $myHtml,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($sendable);

    $sendable->refresh();

    assertMatchesHtmlSnapshot($sendable->contentItem->email_html);
});

it('will not add html tags before the doctype', function () {
    $myHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd"><h1>Hello</h1><p>Hello world</p>';

    $sendable = AutomationMail::factory()->create();
    $sendable->contentItem->update([
        'html' => $myHtml,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($sendable);

    $sendable->refresh();

    assertMatchesHtmlSnapshot($sendable->contentItem->email_html);
});

it('will not change the doctype', function () {
    $myHtml = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><h1>Hello</h1><p>Hello world</p>';

    $sendable = AutomationMail::factory()->create();
    $sendable->contentItem->update([
        'html' => $myHtml,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($sendable);

    $sendable->refresh();

    assertMatchesHtmlSnapshot($sendable->contentItem->email_html);
});

it('will add utm tags', function () {
    $myHtml = '<html><body><h1>Hello</h1><a href="https://spatie.be">Hello world</a></body></html>';

    $sendable = AutomationMail::factory()->create([
        'name' => 'My AutomationMail',
    ]);
    $sendable->contentItem->update([
        'html' => $myHtml,
        'utm_tags' => true,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($sendable);

    $sendable->refresh();

    expect($sendable->contentItem->email_html)->toContain(htmlspecialchars('https://spatie.be?utm_source=newsletter&utm_medium=email&utm_campaign=my-automationmail'));
    assertMatchesHtmlSnapshot($sendable->contentItem->email_html);
});

it('will add utm tags to links that already have query parameters', function () {
    $myHtml = '<html><body><h1>Hello</h1><a href="https://spatie.be?foo=bar">Hello world</a></body></html>';

    $sendable = AutomationMail::factory()->create([
        'name' => 'My AutomationMail',
    ]);
    $sendable->contentItem->update([
        'html' => $myHtml,
        'utm_tags' => true,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($sendable);

    $sendable->refresh();

    expect($sendable->contentItem->email_html)->toContain(htmlspecialchars('https://spatie.be?foo=bar&utm_source=newsletter&utm_medium=email&utm_campaign=my-automationmail'));
    assertMatchesHtmlSnapshot($sendable->contentItem->email_html);
});

it('will add utm tags to urls with paths correctly', function () {
    $myHtml = '<html><body><h1>Hello</h1><a href="https://freek.dev/1234-my-blogpost">Hello world</a></body></html>';

    $sendable = AutomationMail::factory()->create([
        'name' => 'My AutomationMail',
    ]);
    $sendable->contentItem->update([
        'html' => $myHtml,
        'utm_tags' => true,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($sendable);

    $sendable->refresh();

    expect($sendable->contentItem->email_html)->toContain(htmlspecialchars('https://freek.dev/1234-my-blogpost?utm_source=newsletter&utm_medium=email&utm_campaign=my-automationmail'));
    assertMatchesHtmlSnapshot($sendable->contentItem->email_html);
});

it('will add utm tags to urls with paths correctly when the link is added twice', function () {
    $myHtml = '<html><body><h1>Hello</h1><a href="https://freek.dev">Hello world</a><a href="https://freek.dev/1234-my-blogpost">Hello world</a></body></html>';

    $sendable = AutomationMail::factory()->create([
        'name' => 'My AutomationMail',
    ]);
    $sendable->contentItem->update([
        'html' => $myHtml,
        'utm_tags' => true,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($sendable);

    $sendable->refresh();

    expect($sendable->contentItem->email_html)->toContain(htmlspecialchars('https://freek.dev?utm_source=newsletter&utm_medium=email&utm_campaign=my-automationmail'));
    expect($sendable->contentItem->email_html)->toContain(htmlspecialchars('https://freek.dev/1234-my-blogpost?utm_source=newsletter&utm_medium=email&utm_campaign=my-automationmail'));
    assertMatchesHtmlSnapshot($sendable->contentItem->email_html);
});

it('will not change img source', function () {
    $myHtml = '<html><body><h1>Hello</h1><a href="https://freek.dev">Hello world</a><img src="https://freek.dev"/></body></html>';

    $sendable = AutomationMail::factory()->create([
        'name' => 'My AutomationMail',
    ]);
    $sendable->contentItem->update([
        'html' => $myHtml,
        'utm_tags' => true,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($sendable);

    $sendable->refresh();

    expect($sendable->contentItem->email_html)->toContain('<img src="https://freek.dev">');
    assertMatchesHtmlSnapshot($sendable->contentItem->email_html);
});

it('doesnt crash on empty html', function () {
    $myHtml = '';

    $sendable = AutomationMail::factory()->create([
        'name' => 'My AutomationMail',
    ]);
    $sendable->contentItem->update([
        'html' => $myHtml,
        'utm_tags' => true,
    ]);

    app(PrepareEmailHtmlAction::class)->execute($sendable);

    $this->expectNotToPerformAssertions();
});
