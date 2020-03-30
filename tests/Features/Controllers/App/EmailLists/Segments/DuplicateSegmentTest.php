<?php

namespace Spatie\Mailcoach\Tests\Features\Controllers\App\EmailLists\Segments;

use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments\DuplicateSegmentController;
use Spatie\Mailcoach\Http\App\Controllers\EmailLists\Segments\EditSegmentController;
use Spatie\Mailcoach\Models\Tag;
use Spatie\Mailcoach\Models\TagSegment;
use Spatie\Mailcoach\Tests\TestCase;

class DuplicateSegmentTest extends TestCase
{
    /** @test */
    public function it_can_duplicate_a_segment()
    {
        $this->authenticate();

        /** @var \Spatie\Mailcoach\Models\TagSegment $originalSegment */
        $originalSegment = factory(TagSegment::class)->create();

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
            ->assertRedirect(action([EditSegmentController::class, 'edit'], [$originalSegment->emailList->id,  2]));

        /** @var TagSegment $duplicatedSegment */
        $duplicatedSegment = TagSegment::find(2);

        $this->assertEquals(
            "Duplicate of {$originalSegment->name}",
            $duplicatedSegment->name
        );

        $this->assertEquals(
            $positiveTags,
            $duplicatedSegment->positiveTags->map(fn (Tag $tag) => $tag->name)->toArray()
        );

        $this->assertEquals(
            $negativeTags,
            $duplicatedSegment->negativeTags->map(fn (Tag $tag) => $tag->name)->toArray()
        );
    }
}
