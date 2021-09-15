<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Http\Api\Controllers\EmailLists\EmailListsController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);
uses(RespondsToApiRequests::class);

beforeEach(function () {
    test()->loginToApi();
});

it('can list all email lists', function () {
    $emailLists = EmailList::factory(3)->create();

    $this
        ->getJson(action([EmailListsController::class, 'index']))
        ->assertSuccessful()
        ->assertSeeText($emailLists->first()->name);
});

it('can search email lists', function () {
    EmailList::factory()->create([
        'name' => 'one',
    ]);

    EmailList::factory()->create([
        'name' => 'two',
    ]);

    $this
        ->getJson(action([EmailListsController::class, 'index']) . '?filter[search]=two')
        ->assertSuccessful()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['name' => 'two']);
});

test('the api can show an email list', function () {
    $emailList = EmailList::factory()->create();

    $this
        ->getJson(action([EmailListsController::class, 'show'], $emailList))
        ->assertSuccessful()
        ->assertJsonFragment(['name' => $emailList->name]);
});

test('an email list can be stored using the api', function () {
    $attributes = [
        'name' => 'email list name',
        'default_from_email' => 'johndoe@example.com',
        'default_from_name' => 'john doe',
        'default_reply_to_email' => 'johndoe@example.com',
        'default_reply_to_name' => 'john doe',
    ];

    $this
        ->postJson(action([EmailListsController::class, 'store'], $attributes))
        ->assertSuccessful();

    test()->assertDatabaseHas(static::getEmailListTableName(), $attributes);
});

test('an email list can be updated using the api', function () {
    $emailList = EmailList::factory()->create();
    $id = $emailList->id;

    $attributes = [
        'name' => 'email list name',
        'default_from_email' => 'johndoe@example.com',
        'default_from_name' => 'john doe',
        'default_reply_to_email' => 'johndoe@example.com',
        'default_reply_to_name' => 'john doe',
    ];

    $this
        ->putJson(action([EmailListsController::class, 'update'], $emailList), $attributes)
        ->assertSuccessful();

    $emailList = $emailList->refresh();

    test()->assertEquals($id, $emailList->id);
    test()->assertEquals($attributes['name'], $emailList->name);
    test()->assertEquals($attributes['default_from_email'], $emailList->default_from_email);
    test()->assertEquals($attributes['default_from_name'], $emailList->default_from_name);
    test()->assertEquals($attributes['default_reply_to_email'], $emailList->default_reply_to_email);
    test()->assertEquals($attributes['default_reply_to_name'], $emailList->default_reply_to_name);
});

test('an email list can be deleted using the api', function () {
    $template = EmailList::factory()->create();

    $this
        ->deleteJson(action([EmailListsController::class, 'destroy'], $template))
        ->assertSuccessful();

    test()->assertCount(0, EmailList::get());
});
