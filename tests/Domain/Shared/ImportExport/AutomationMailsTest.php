<?php

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Content\Models\Link;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportAutomationMailsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportAutomationMailsJob;

beforeEach(function () {
    $this->disk = setupImportDisk();
});

it('can export and import automation mails', function () {
    $automationMails = AutomationMail::factory(10)->create();
    Link::factory()->create(['content_item_id' => $automationMails->first()->contentItem->id]);

    (new ExportAutomationMailsJob('import', $automationMails->pluck('id')->toArray()))->handle();

    expect($this->disk->exists('import/automation_mails.csv'))->toBeTrue();
    expect($this->disk->exists('import/automation_mail_links.csv'))->toBeTrue();

    AutomationMail::query()->delete();
    ContentItem::query()->delete();
    Link::query()->delete();

    expect(AutomationMail::count())->toBe(0);
    expect(ContentItem::count())->toBe(0);
    expect(Link::count())->toBe(0);

    (new ImportAutomationMailsJob())->handle();
    (new ImportAutomationMailsJob())->handle(); // Don't import duplicates

    expect(AutomationMail::count())->toBe(10);
    expect(ContentItem::count())->toBe(10);
    expect(Link::count())->toBe(1);
});
