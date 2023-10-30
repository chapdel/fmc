<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportSegmentsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportSegmentsJob;

beforeEach(function () {
    $this->disk = setupImportDisk();
});

it('can export and import segments', function () {
    $emailList = EmailList::factory()->create();
    TagSegment::factory()->create(['email_list_id' => $emailList->id]);

    (new ExportSegmentsJob('import', [$emailList->id]))->handle();

    expect($this->disk->exists('import/segments.csv'))->toBeTrue();

    TagSegment::query()->delete();

    expect(TagSegment::count())->toBe(0);

    (new ImportSegmentsJob())->handle();
    (new ImportSegmentsJob())->handle(); // Don't import duplicates

    expect(TagSegment::count())->toBe(1);
});
