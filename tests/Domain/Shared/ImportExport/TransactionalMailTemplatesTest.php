<?php

use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportTransactionalMailTemplatesJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportTransactionalMailTemplatesJob;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

beforeEach(function () {
    $this->disk = setupImportDisk();
});

it('can export and import transactional mail templates', function () {
    $templates = TransactionalMail::factory(10)->create();

    (new ExportTransactionalMailTemplatesJob($this->disk->path('import'), $templates->pluck('id')->toArray()))->handle();

    expect($this->disk->exists('import/transactional_mail_templates.csv'))->toBeTrue();

    TransactionalMail::query()->delete();

    expect(TransactionalMail::count())->toBe(0);

    (new ImportTransactionalMailTemplatesJob())->handle();
    (new ImportTransactionalMailTemplatesJob())->handle(); // Don't import duplicates

    expect(TransactionalMail::count())->toBe(10);
});
