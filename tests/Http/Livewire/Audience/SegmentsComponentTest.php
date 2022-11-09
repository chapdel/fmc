<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Http\App\Livewire\Audience\SegmentsComponent;

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

    Livewire::test(SegmentsComponent::class)
        ->call('duplicateSegment', $originalSegment->id)
        ->assertRedirect(route('mailcoach.emailLists.segments.edit', [$originalSegment->emailList, TagSegment::orderByDesc('id')->first()]));

    /** @var TagSegment $duplicatedSegment */
    $duplicatedSegment = TagSegment::orderByDesc('id')->first();

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
