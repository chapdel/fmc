<?php

use Spatie\Mailcoach\Domain\Automation\Models\AutomationMail;
use Spatie\Mailcoach\Domain\Automation\Models\AutomationMailLink;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportAutomationMailsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportAutomationMailsJob;

beforeEach(function () {
    $this->disk = setupImportDisk();
});

it('can export and import automation mails', function () {
    $automationMails = AutomationMail::factory(10)->create();
    AutomationMailLink::factory()->create(['automation_mail_id' => $automationMails->first()->id]);

    (new ExportAutomationMailsJob($this->disk->path('import'), $automationMails->pluck('id')->toArray()))->handle();

    expect($this->disk->exists('import/automation_mails.csv'))->toBeTrue();
    expect($this->disk->exists('import/automation_mail_links.csv'))->toBeTrue();

    AutomationMail::query()->delete();
    AutomationMailLink::query()->delete();

    expect(AutomationMail::count())->toBe(0);
    expect(AutomationMailLink::count())->toBe(0);

    (new ImportAutomationMailsJob())->handle();
    (new ImportAutomationMailsJob())->handle(); // Don't import duplicates

    expect(AutomationMail::count())->toBe(10);
    expect(AutomationMailLink::count())->toBe(1);
});
