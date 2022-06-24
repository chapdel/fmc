<?php

use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportTransactionalMailTemplatesJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportTransactionalMailTemplatesJob;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMailTemplate;

beforeEach(function () {
    $this->disk = setupImportDisk();
});

it('can export and import transactional mail templates', function () {
    $templates = TransactionalMailTemplate::factory(10)->create();

    (new ExportTransactionalMailTemplatesJob($this->disk->path('import'), $templates->pluck('id')->toArray()))->handle();

    expect($this->disk->exists('import/transactional_mail_templates.csv'))->toBeTrue();

    TransactionalMailTemplate::query()->delete();

    expect(TransactionalMailTemplate::count())->toBe(0);

    (new ImportTransactionalMailTemplatesJob())->handle();
    (new ImportTransactionalMailTemplatesJob())->handle(); // Don't import duplicates

    expect(TransactionalMailTemplate::count())->toBe(10);
});
