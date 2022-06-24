<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportSubscribersJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportSubscribersJob;

beforeEach(function () {
    $this->disk = setupImportDisk();
});

it('can export and import subscribers', function () {
    $emailList = EmailList::factory()->create();
    Subscriber::factory(10)->create([
        'email_list_id' => $emailList->id,
    ]);

    (new ExportSubscribersJob($this->disk->path('import'), [$emailList->id]))->handle();

    expect($this->disk->exists('import/subscribers-1.csv'))->toBeTrue();

    Subscriber::query()->delete();

    expect(Subscriber::count())->toBe(0);

    (new ImportSubscribersJob())->handle();
    (new ImportSubscribersJob())->handle(); // Don't import duplicates

    expect(Subscriber::count())->toBe(10);
    expect(Subscriber::first()->emailList->is($emailList))->toBeTrue();
});
