<?php

use Illuminate\Support\Facades\DB;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Shared\Jobs\Export\ExportTagsJob;
use Spatie\Mailcoach\Domain\Shared\Jobs\Import\ImportSubscriberTagsJob;

beforeEach(function () {
    $this->disk = setupImportDisk();
});

it('can export and import subscriber tags', function () {
    $emailList = EmailList::factory()->create();
    $tag = Tag::factory()->create([
        'email_list_id' => $emailList->id,
    ]);
    $subscriber = Subscriber::factory()->create(['email_list_id' => $emailList->id]);
    $subscriber->addTag($tag->name);

    (new ExportTagsJob('import', [$emailList->id]))->handle();

    expect($this->disk->exists('import/email_list_subscriber_tags-1.csv'))->toBeTrue();

    $subscriber->tags()->detach();
    expect($subscriber->fresh()->hasTag($tag->name))->toBe(false);

    expect(DB::table('mailcoach_email_list_subscriber_tags')->count())->toBe(0);

    (new ImportSubscriberTagsJob())->handle();
    (new ImportSubscriberTagsJob())->handle(); // Don't import duplicates

    expect(DB::table('mailcoach_email_list_subscriber_tags')->count())->toBe(1);
    expect($subscriber->hasTag($tag->name))->toBe(true);
});
