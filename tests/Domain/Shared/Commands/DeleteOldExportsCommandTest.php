<?php

use Illuminate\Support\Facades\Storage;
use Spatie\Mailcoach\Domain\Shared\Commands\DeleteOldExportsCommand;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    TestTime::unfreeze();

    Storage::fake(config('mailcoach.export_disk'));

    $this->disk = Storage::disk(config('mailcoach.export_disk'));
});

it('will delete old exports', function (string $path, int $subDays, bool $expectExists) {
    $this->disk->put($path, 'zip contents');
    $fullPath = $this->disk->path($path);
    touch($fullPath, now()->subDays($subDays)->startOfDay()->timestamp);

    $this->artisan(DeleteOldExportsCommand::class)->assertSuccessful();

    $expectExists
        ? $this->disk->assertExists($path)
        : $this->disk->assertMissing($path);
})->with([
    // old files in mailcoach-exports will get deleted
    ['mailcoach-exports/export.zip', 2, true],
    ['mailcoach-exports/export.zip', 3, true],
    ['mailcoach-exports/export.zip', 4, false],

    // old files in subdirectory of mailcoach-exports will get deleted
    ['mailcoach-exports/subdirectory/export.zip', 2, true],
    ['mailcoach-exports/subdirectory/export.zip', 3, true],
    ['mailcoach-exports/subdirectory/export.zip', 4, false],

    // files in root will remain
    ['export.zip', 2, true],
    ['export.zip', 3, true],
    ['export.zip', 4, true],

    // files in another directory will remain
    ['other/export.zip', 2, true],
    ['other/export.zip', 3, true],
    ['other/export.zip', 4, true],
]);
