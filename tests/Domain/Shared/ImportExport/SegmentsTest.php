<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportSegmentsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportSegmentsJob;

beforeEach(function () {
    $this->disk = setupImportDisk();
});

it('can export and import segments', function () {
    $emailList = EmailList::factory()->create();
    $positiveTag = Tag::factory()->create([
        'email_list_id' => $emailList->id,
    ]);
    $negativeTag = Tag::factory()->create([
        'email_list_id' => $emailList->id,
    ]);
    $segment = TagSegment::factory()->create(['email_list_id' => $emailList->id]);
    $segment->syncPositiveTags([$positiveTag->name]);
    $segment->syncNegativeTags([$negativeTag->name]);

    (new ExportSegmentsJob($this->disk->path('import'), [$emailList->id]))->handle();

    expect($this->disk->exists('import/segments.csv'))->toBeTrue();
    expect($this->disk->exists('import/positive_segment_tags.csv'))->toBeTrue();
    expect($this->disk->exists('import/negative_segment_tags.csv'))->toBeTrue();

    TagSegment::query()->delete();

    expect(TagSegment::count())->toBe(0);

    (new ImportSegmentsJob())->handle();
    (new ImportSegmentsJob())->handle(); // Don't import duplicates

    expect(TagSegment::count())->toBe(1);
    expect(TagSegment::first()->positiveTags()->count())->toBe(1);
    expect(TagSegment::first()->negativeTags()->count())->toBe(1);
});
