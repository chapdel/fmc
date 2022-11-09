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
    $this->disk->put('import/mailcoach-export.zip', File::get(__DIR__.'/stubs/mailcoach-export.zip'));

    (new UnzipImportJob('import/mailcoach-export.zip'))->handle();

    expect($this->disk->exists('import/automation_actions.csv'))->toBeTrue();
    expect($this->disk->exists('import/automation_mails.csv'))->toBeTrue();
    expect($this->disk->exists('import/automation_triggers.csv'))->toBeTrue();
    expect($this->disk->exists('import/automations.csv'))->toBeTrue();
    expect($this->disk->exists('import/campaign_links.csv'))->toBeTrue();
    expect($this->disk->exists('import/campaigns.csv'))->toBeTrue();
    expect($this->disk->exists('import/email_list_allow_form_subscription_tags.csv'))->toBeTrue();
    expect($this->disk->exists('import/email_lists.csv'))->toBeTrue();
    expect($this->disk->exists('import/negative_segment_tags.csv'))->toBeTrue();
    expect($this->disk->exists('import/positive_segment_tags.csv'))->toBeTrue();
    expect($this->disk->exists('import/segments.csv'))->toBeTrue();
    expect($this->disk->exists('import/tags.csv'))->toBeTrue();
    expect($this->disk->exists('import/templates.csv'))->toBeTrue();
    expect($this->disk->exists('import/transactional_mail_templates.csv'))->toBeTrue();
});
