<?php

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function setupImportDisk(): Filesystem
{
    Storage::fake();
    File::ensureDirectoryExists(Storage::disk(config('mailcoach.import_disk'))->path('import'));

    return Storage::disk(config('mailcoach.import_disk'));
}

expect()->extend('timePassedInSeconds', function ($expectedPassedInSeconds) {
    $actualPassedInSeconds = $this->value->diffInSeconds();

    expect($actualPassedInSeconds)->toBe($expectedPassedInSeconds);
});
