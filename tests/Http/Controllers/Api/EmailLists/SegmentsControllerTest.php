<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Audience\Models\TagSegment;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\SegmentsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();
});

it('can list all tagSegments', function () {
    $emailList = EmailList::factory()->create();
    $tagSegments = TagSegment::factory(3)->create([
        'email_list_id' => $emailList->id,
    ]);

    $this
        ->getJson(action([SegmentsController::class, 'index'], $emailList))
        ->assertSuccessful()
        ->assertSeeText($tagSegments->first()->name)
        ->assertSeeText($tagSegments->nth(2)->first()->name)
        ->assertSeeText($tagSegments->nth(3)->first()->name);
});

test('the api can show an tagSegment', function () {
    $emailList = EmailList::factory()->create();
    $tagSegment = TagSegment::factory()->create([
        'email_list_id' => $emailList,
    ]);

    $this
        ->getJson(action([SegmentsController::class, 'show'], [$tagSegment->emailList, $tagSegment]))
        ->assertSuccessful()
        ->assertJsonFragment(['name' => $tagSegment->name]);
});

test('an tagSegment can be stored using the api', function () {
    $emailList = EmailList::factory()->create();
    $tag = Tag::factory()->create(['email_list_id' => $emailList->id]);

    $attributes = [
        'name' => 'tagSegment name',
        'positive_tags' => [$tag->name],
    ];

    $this
        ->postJson(action([SegmentsController::class, 'store'], $emailList), $attributes)
        ->assertSuccessful();

    expect(TagSegment::count())->toBe(1);

    $segment = TagSegment::first();
    expect($segment->name)->toBe('tagSegment name');
    expect($segment->stored_conditions->count())->toBe(1);
});

test('an tagSegment can be updated using the api', function () {
    $emailList = EmailList::factory()->create();
    $tag = Tag::factory()->create(['email_list_id' => $emailList->id]);

    $tagSegment = TagSegment::factory()->create([
        'email_list_id' => $emailList,
    ]);

    $attributes = [
        'name' => 'updated name',
        'positive_tags' => [$tag->name],
    ];

    $this
        ->putJson(action([SegmentsController::class, 'update'], [$emailList, $tagSegment]), $attributes)
        ->assertSuccessful();

    $tagSegment = $tagSegment->refresh();

    expect($tagSegment->name)->toEqual($attributes['name']);
    expect($tagSegment->stored_conditions->count())->toBe(1);
});

test('an tagSegment can be deleted using the api', function () {
    $emailList = EmailList::factory()->create();
    $tagSegment = TagSegment::factory()->create([
        'email_list_id' => $emailList,
    ]);

    expect(TagSegment::get())->toHaveCount(1);

    $this
        ->deleteJson(action([SegmentsController::class, 'destroy'], [$emailList, $tagSegment]))
        ->assertSuccessful();

    expect(TagSegment::get())->toHaveCount(0);
});
