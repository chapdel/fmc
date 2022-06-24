<?php

use Illuminate\Support\Facades\Event;
use Spatie\Mailcoach\Domain\Shared\Events\ImportFinishedEvent;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\CleanupImportJob;

beforeEach(function () {
    $this->disk = setupImportDisk();
});

it('can cleanup the import folders', function () {
    Event::fake();

    expect($this->disk->exists('import'))->toBeTrue();

    (new CleanupImportJob())->handle();

    expect($this->disk->exists('import'))->toBeFalse();
    Event::assertDispatched(ImportFinishedEvent::class);
});
