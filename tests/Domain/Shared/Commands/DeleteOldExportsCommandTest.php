<?php

use Illuminate\Support\Facades\Storage;
use Spatie\Mailcoach\Domain\Shared\Commands\DeleteOldExportsCommand;
use Spatie\TestTime\TestTime;

beforeEach(function () {
    TestTime::unfreeze();

    Storage::fake(config('mailcoach.export_disk'));

    $this->disk = Storage::disk(config('mailcoach.export_disk'));
});

it('will delete old exports', function (int $subDays, bool $expectExists) {
    $this->disk->put('export.zip', 'zip contents');
    $fullPath = $this->disk->path('export.zip');
    touch($fullPath, now()->subDays($subDays)->startOfDay()->timestamp);

    $this->artisan(DeleteOldExportsCommand::class)->assertSuccessful();

    $expectExists
        ?  $this->disk->assertExists('export.zip')
        :  $this->disk->assertMissing('export.zip');
})->with([
    [2, true],
    [3, true],
    [4, false],
]);
