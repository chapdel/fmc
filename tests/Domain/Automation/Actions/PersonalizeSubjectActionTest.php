<?php

use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Automation\Actions\PersonalizeSubjectAction;

beforeEach(function () {
    test()->send = SendFactory::new()->create();

    $subscriber = test()->send->subscriber;
    $subscriber->uuid = 'my-uuid';
    $subscriber->extra_attributes = ['first_name' => 'John', 'last_name' => 'Doe'];
    $subscriber->save();

    test()->send->campaign->update(['name' => 'my campaign']);

    test()->personalizeSubjectAction = app(PersonalizeSubjectAction::class);
});

it('can replace an placeholder for a subscriber attribute', function () {
    assertPersonalizeSubjectActionResult('::subscriber.uuid::', 'my-uuid');
});

it('will not replace a non existing attribute', function () {
    assertPersonalizeSubjectActionResult('::subscriber.non-existing::', '::subscriber.non-existing::');
});

it('can replace an placeholder for a subscriber extra attribute', function () {
    assertPersonalizeSubjectActionResult('::subscriber.extra_attributes.first_name::', 'John');
});

it('will not replace an placeholder for a non existing subscriber extra attribute', function () {
    assertPersonalizeSubjectActionResult('::subscriber.extra_attributes.non-existing::', '::subscriber.extra_attributes.non-existing::');
});

// Helpers
function assertPersonalizeSubjectActionResult(string $originalSubject, $expectedSubject)
{
    $actualOutputHtml = app(PersonalizeSubjectAction::class)->execute($originalSubject, test()->send);
    test()->assertEquals($expectedSubject, $actualOutputHtml, "The personalize action did not produce the expected result. Expected: `{$expectedSubject}`, actual: `{$actualOutputHtml}`");

    $expectedOutputHtmlWithHtmlTags = "{$expectedSubject}";
    $actualOutputHtmlWithHtmlTags = app(PersonalizeSubjectAction::class)->execute("$originalSubject", test()->send);

    test()->assertEquals($expectedOutputHtmlWithHtmlTags, $actualOutputHtmlWithHtmlTags, "The personalize action did not produce the expected result when wrapped in html tags. Expected: `{$expectedOutputHtmlWithHtmlTags}`, actual: `{$actualOutputHtmlWithHtmlTags}`");
}
