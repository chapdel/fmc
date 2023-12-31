<?php

use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportTemplatesJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportTemplatesJob;
use Spatie\Mailcoach\Domain\Template\Models\Template;

beforeEach(function () {
    $this->disk = setupImportDisk();
});

it('can export and import templates', function () {
    $templates = Template::factory(10)->create();

    (new ExportTemplatesJob('import', $templates->pluck('id')->toArray()))->handle();

    expect($this->disk->exists('import/templates.csv'))->toBeTrue();

    Template::query()->delete();

    expect(Template::count())->toBe(0);

    (new ImportTemplatesJob())->handle();
    (new ImportTemplatesJob())->handle(); // Don't import duplicates

    expect(Template::count())->toBe(10);
});
