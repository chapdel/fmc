<?php

use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Campaign\Actions\PersonalizeSubjectAction;

beforeEach(function () {
    test()->send = SendFactory::new()->create();

    $subscriber = test()->send->subscriber;
    $subscriber->uuid = 'my-uuid';
    $subscriber->extra_attributes = ['first_name' => 'John', 'last_name' => 'Doe'];
    $subscriber->save();

    test()->send->campaign->update(['name' => 'my campaign']);

    test()->personalizeSubjectAction = new PersonalizeSubjectAction();
});

it('can replace an placeholder for a subscriber attribute', function () {
    assertPersonalizeCampaignSubjectActionResult('::subscriber.uuid::', 'my-uuid');
});

it('will not replace a non existing attribute', function () {
    assertPersonalizeCampaignSubjectActionResult('::subscriber.non-existing::', '::subscriber.non-existing::');
});

it('can replace an placeholder for a subscriber extra attribute', function () {
    assertPersonalizeCampaignSubjectActionResult('::subscriber.extra_attributes.first_name::', 'John');
});

it('will not replace an placeholder for a non existing subscriber extra attribute', function () {
    assertPersonalizeCampaignSubjectActionResult('::subscriber.extra_attributes.non-existing::', '::subscriber.extra_attributes.non-existing::');
});

// Helpers
function assertPersonalizeCampaignSubjectActionResult(string $originalSubject, $expectedSubject)
{
    $actualOutputHtml = (new PersonalizeSubjectAction())->execute($originalSubject, test()->send);
    test()->assertEquals($expectedSubject, $actualOutputHtml, "The personalize action did not produce the expected result. Expected: `{$expectedSubject}`, actual: `{$actualOutputHtml}`");

    $expectedOutputHtmlWithHtmlTags = "{$expectedSubject}";
    $actualOutputHtmlWithHtmlTags = (new PersonalizeSubjectAction())->execute("$originalSubject", test()->send);

    test()->assertEquals($expectedOutputHtmlWithHtmlTags, $actualOutputHtmlWithHtmlTags, "The personalize action did not produce the expected result when wrapped in html tags. Expected: `{$expectedOutputHtmlWithHtmlTags}`, actual: `{$actualOutputHtmlWithHtmlTags}`");
}