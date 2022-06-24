<?php

use Illuminate\Support\Facades\File;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ZipExportJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\UnzipImportJob;

beforeEach(function () {
    $this->disk = setupImportDisk();
});

it('can zip an export', function () {
    (new ZipExportJob($this->disk->path('import')))->handle();

    expect($this->disk->exists('import/mailcoach-export.zip'))->toBeTrue();
});

it('can unzip an uploaded export', function () {
    File::copy(__DIR__ . '/stubs/mailcoach-export.zip', $this->disk->path('import/mailcoach-export.zip'));

    (new UnzipImportJob('import/mailcoach-export.zip'))->handle();

    $files = [
        'automation_actions.csv',
        'automation_mails.csv',
        'automation_triggers.csv',
        'automations.csv',
        'campaign_links.csv',
        'campaigns.csv',
        'email_list_allow_form_subscription_tags.csv',
        'email_lists.csv',
        'negative_segment_tags.csv',
        'positive_segment_tags.csv',
        'segments.csv',
        'tags.csv',
        'templates.csv',
        'transactional_mail_templates.csv',
    ];

    foreach ($files as $file) {
        expect($this->disk->exists("import/{$file}"))->toBeTrue();
    }
});
