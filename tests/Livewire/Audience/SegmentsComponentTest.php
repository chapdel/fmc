<?php

use Livewire\Livewire;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Livewire\Audience\SegmentsComponent;

it('can duplicate a segment', function () {
    test()->authenticate();

    /** @var \Spatie\Mailcoach\Domain\Audience\Models\TagSegment $originalSegment */
    $originalSegment = TagSegment::factory()->create();

    Livewire::test(SegmentsComponent::class, ['emailList' => $originalSegment->emailList])
        ->call('duplicateSegment', $originalSegment)
        ->assertRedirect(route('mailcoach.emailLists.segments.edit', [$originalSegment->emailList, TagSegment::orderByDesc('id')->first()]));

    /** @var TagSegment $duplicatedSegment */
    $duplicatedSegment = TagSegment::orderByDesc('id')->first();

    expect($duplicatedSegment->name)->toBe("{$originalSegment->name} - copy");
});
