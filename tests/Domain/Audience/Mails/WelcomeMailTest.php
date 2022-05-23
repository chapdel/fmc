<?php

use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Mails\WelcomeMail;

beforeEach(function () {
    test()->emailList = EmailList::factory()->create([
        'name' => 'my newsletter',
        'requires_confirmation' => false,
        'transactional_mailer' => 'some-transactional-mailer',
    ]);
});


test('the welcome mail has default content', function () {
    test()->emailList->update(['transactional_mailer' => 'log']);

    $subscriber = Subscriber::createWithEmail('john@example.com', ['first_name' => 'John'])->subscribeTo(test()->emailList);

    $content = (new WelcomeMail($subscriber))->render();

    expect($content)->toContain('You are now subscribed');
});
