<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\TagsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;

uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();
});

it('can list all tags', function () {
    $emailList = EmailList::factory()->create();
    $tags = Tag::factory(3)->create([
        'email_list_id' => $emailList->id,
    ]);

    $this
        ->getJson(action([TagsController::class, 'index'], $emailList))
        ->assertSuccessful()
        ->assertSeeText($tags->first()->name)
        ->assertSeeText($tags->nth(2)->first()->name)
        ->assertSeeText($tags->nth(3)->first()->name);
});

test('the api can show an tag', function () {
    $emailList = EmailList::factory()->create();
    $tag = Tag::factory()->create([
        'email_list_id' => $emailList,
    ]);

    $this
        ->getJson(action([TagsController::class, 'show'], [$tag->emailList, $tag]))
        ->assertSuccessful()
        ->assertJsonFragment(['name' => $tag->name]);
});

test('an tag can be stored using the api', function () {
    $emailList = EmailList::factory()->create();

    $attributes = [
        'name' => 'tag name',
    ];

    $this
        ->postJson(action([TagsController::class, 'store'], $emailList), $attributes)
        ->assertSuccessful();

    test()->assertDatabaseHas(Tag::class, $attributes);
});

test('an tag can be updated using the api', function () {
    $emailList = EmailList::factory()->create();
    $tag = Tag::factory()->create([
        'email_list_id' => $emailList,
    ]);

    $attributes = [
        'name' => 'updated name',
    ];

    $this
        ->putJson(action([TagsController::class, 'update'], [$emailList, $tag]), $attributes)
        ->assertSuccessful();

    $tag = $tag->refresh();

    expect($tag->name)->toEqual($attributes['name']);
});

test('an tag can be deleted using the api', function () {
    $emailList = EmailList::factory()->create();
    $tag = Tag::factory()->create([
        'email_list_id' => $emailList,
    ]);

    expect(Tag::get())->toHaveCount(1);

    $this
        ->deleteJson(action([TagsController::class, 'destroy'], [$emailList, $tag]))
        ->assertSuccessful();

    expect(Tag::get())->toHaveCount(0);
});
