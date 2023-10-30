<?php

use Illuminate\Support\Facades\Bus;
use Spatie\Mailcoach\Domain\Content\Jobs\CalculateStatisticsJob;
use Spatie\Mailcoach\Domain\Content\Models\ContentItem;
use Spatie\Mailcoach\Domain\Shared\Models\Send;

beforeEach(function () {
    $this->contentItem = ContentItem::factory()->create();
});

it('can dispatch a job to recalculate statistics', function () {
    Bus::fake();

    $this->contentItem->dispatchCalculateStatistics();

    Bus::assertDispatched(CalculateStatisticsJob::class, 1);
});

it('will not dispatch the recalculation job twice', function () {
    Bus::fake();

    $this->contentItem->dispatchCalculateStatistics();
    $this->contentItem->dispatchCalculateStatistics();

    Bus::assertDispatched(CalculateStatisticsJob::class, 1);
});

it('can dispatch the recalculation job again after the previous job has run', function () {
    Bus::fake();

    $this->contentItem->dispatchCalculateStatistics();

    (new CalculateStatisticsJob($this->contentItem))->handle();

    Send::factory()->create([
        'content_item_id' => $this->contentItem->id,
    ]);

    cache()->lock(
        'laravel_unique_job:'.CalculateStatisticsJob::class.$this->contentItem->uuid
    )->forceRelease(); // Lock released

    $this->contentItem->dispatchCalculateStatistics();

    Bus::assertDispatched(CalculateStatisticsJob::class, 2);
});
