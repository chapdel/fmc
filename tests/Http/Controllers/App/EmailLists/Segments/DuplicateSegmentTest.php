<?php

use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments\DuplicateSegmentController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments\EditSegmentController;

it('can duplicate a segment', function () {
    test()->authenticate();

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\TagSegment $originalSegment */
    $originalSegment = TagSegment::factory()->create();

    $positiveTags = ['positive-1', 'positive-2'];
    $negativeTags = ['negative-1', 'negative-2'];

    foreach (array_merge($positiveTags, $negativeTags) as $tagName) {
        $originalSegment->emailList->tags()->create(['name' => $tagName]);
    }

    $originalSegment
        ->syncPositiveTags($positiveTags)
        ->syncNegativeTags($negativeTags);

    $duplicateSegmentEndpoint = action(DuplicateSegmentController::class, [
        $originalSegment->emailList,
        $originalSegment,
    ]);

    $this
        ->post($duplicateSegmentEndpoint)
        ->assertRedirect(action([EditSegmentController::class, 'edit'], [$originalSegment->emailList->id, TagSegment::orderByDesc('id')->first()->id]));

    /** @var TagSegment $duplicatedSegment */
    $duplicatedSegment = TagSegment::find(TagSegment::orderByDesc('id')->first()->id);

    test()->assertEquals(
        "Duplicate of {$originalSegment->name}",
        $duplicatedSegment->name
    );

    test()->assertEquals(
        $positiveTags,
        $duplicatedSegment->positiveTags->map(fn (Tag $tag) => $tag->name)->toArray()
    );

    test()->assertEquals(
        $negativeTags,
        $duplicatedSegment->negativeTags->map(fn (Tag $tag) => $tag->name)->toArray()
    );
});
