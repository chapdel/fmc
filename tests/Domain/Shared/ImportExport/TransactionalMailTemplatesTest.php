<?php

use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportTransactionalMailTemplatesJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportTransactionalMailTemplatesJob;
use Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail;

beforeEach(function () {
    $this->disk = setupImportDisk();
});

it('can export and import transactional mail templates', function () {
    $templates = TransactionalMail::factory(10)->create();
    TransactionalMail::query()->each(function ($template) {
        $template->contentItem->update(['subject' => 'a subject', 'html' => 'some html']);
    });

    (new ExportTransactionalMailTemplatesJob('import', $templates->pluck('id')->toArray()))->handle();

    expect($this->disk->exists('import/transactional_mail_templates.csv'))->toBeTrue();

    TransactionalMail::query()->delete();

    expect(TransactionalMail::count())->toBe(0);

    (new ImportTransactionalMailTemplatesJob())->handle();
    (new ImportTransactionalMailTemplatesJob())->handle(); // Don't import duplicates

    expect(TransactionalMail::count())->toBe(10);
    expect(TransactionalMail::first()->contentItem->subject)->toBe('a subject');
    expect(TransactionalMail::first()->contentItem->html)->toBe('some html');
});
