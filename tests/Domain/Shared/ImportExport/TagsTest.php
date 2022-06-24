<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportEmailListsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportTagsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportEmailListsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportTagsJob;

beforeEach(function () {
    $this->disk = setupImportDisk();
});

it('can export and import tags', function () {
    $emailList = EmailList::factory()->create();
    Tag::factory(10)->create([
        'email_list_id' => $emailList->id,
    ]);

    (new ExportEmailListsJob($this->disk->path('import'), [$emailList->id]))->handle();
    (new ExportTagsJob($this->disk->path('import'), [$emailList->id]))->handle();

    expect($this->disk->exists('import/tags.csv'))->toBeTrue();

    Tag::query()->delete();

    expect(Tag::count())->toBe(0);

    (new ImportEmailListsJob())->handle();
    (new ImportTagsJob())->handle();
    (new ImportTagsJob())->handle(); // Don't import duplicates

    expect(Tag::count())->toBe(10);
    expect(Tag::first()->emailList->is($emailList))->toBeTrue();
});
