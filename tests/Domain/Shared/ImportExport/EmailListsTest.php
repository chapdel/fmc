<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportEmailListsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportTagsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportEmailListsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportTagsJob;

beforeEach(function () {
    $this->disk = setupImportDisk();
});

it('can export and import email lists', function () {
    $emailList = EmailList::factory()->create();
    $emailList2 = EmailList::factory()->create();

    (new ExportEmailListsJob($this->disk->path('import'), [$emailList->id]))->handle();

    expect(EmailList::count())->toBe(2);
    expect($this->disk->exists('import/email_lists.csv'))->toBeTrue();

    EmailList::query()->delete();
    expect(EmailList::count())->toBe(0);

    (new ImportEmailListsJob())->handle();

    expect(EmailList::count())->toBe(1);
    expect(EmailList::first()->uuid)->toBe($emailList->uuid);
});

it('also exports and imports form subscription tags', function () {
    $emailList = EmailList::factory()->create();
    $tag = Tag::factory()->create();

    $emailList->allowedFormSubscriptionTags()->attach($tag);

    expect($emailList->allowedFormSubscriptionTags()->count())->toBe(1);

    (new ExportTagsJob($this->disk->path('import'), [$emailList->id]))->handle();
    (new ExportEmailListsJob($this->disk->path('import'), [$emailList->id]))->handle();

    expect($this->disk->exists('import/email_list_allow_form_subscription_tags.csv'))->toBeTrue();
    expect($this->disk->exists('import/tags.csv'))->toBeTrue();

    EmailList::query()->delete();
    Tag::query()->delete();

    (new ImportEmailListsJob())->handle();
    (new ImportEmailListsJob())->handle(); // Don't import duplicates
    (new ImportTagsJob())->handle();

    expect(Tag::count())->toBe(1);
    expect(EmailList::first()->allowedFormSubscriptionTags()->count())->toBe(1);
});
