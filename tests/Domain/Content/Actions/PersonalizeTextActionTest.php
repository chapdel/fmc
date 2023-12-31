<?php

use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Content\Actions\PersonalizeTextAction;

beforeEach(function () {
    test()->send = SendFactory::new()->create();

    $subscriber = test()->send->subscriber;
    $subscriber->uuid = 'my-uuid';
    $subscriber->extra_attributes = ['first_name' => 'John', 'last_name' => 'Doe'];
    $subscriber->save();

    test()->send->contentItem->model->update(['name' => 'my campaign']);
});

it('can replace an placeholder for a subscriber attribute', function () {
    assertResult('::subscriber.uuid::', 'my-uuid');
});

it('will not replace a non existing attribute', function () {
    assertResult('::subscriber.non-existing::', '::subscriber.non-existing::');
});

it('can replace an placeholder for a subscriber extra attribute', function () {
    assertResult('::subscriber.extra_attributes.first_name::', 'John');
});

it('will not replace an placeholder for a non existing subscriber extra attribute', function () {
    assertResult('::subscriber.extra_attributes.non-existing::', '::subscriber.extra_attributes.non-existing::');
});

it('can replace unsubscribe url', function () {
    assertResult('::unsubscribeUrl::', test()->send->subscriber->unsubscribeUrl(test()->send));
});

it('can replace unsubscribe tag url', function () {
    test()->send->subscriber->addTag('some tag');

    assertResult('::unsubscribeTag::some tag::', test()->send->subscriber->unsubscribeTagUrl('some tag', test()->send));
});

it('can use twig templating for replacers', function () {
    assertResult('{{subscriber.uuid}}', 'my-uuid');
    assertResult('{{subscriber.first_name}}', 'John');
    assertResult('{{subscriber.extra_attributes.first_name}}', 'John');
    assertResult('{{subscriber.last_name}}', 'Doe');
    assertResult('{{subscriber.coupon}}', '');
    assertResult('{{ this_does_not_exist }}', '');
    assertResult('{{campaign.name}}', 'my campaign');
    assertResult('{% if subscriber.first_name %}Hello {{subscriber.first_name}}{% endif %}', 'Hello John');
    assertResult('{%if not subscriber.coupon %}No coupon{% endif %}', 'No coupon');
});

// Helpers
function assertResult(string $inputHtml, $expectedOutputHtml)
{
    $actualOutputHtml = app(PersonalizeTextAction::class)->execute($inputHtml, test()->send);
    expect($actualOutputHtml)->toEqual($expectedOutputHtml, "The personalize action did not produce the expected result. Expected: `{$expectedOutputHtml}`, actual: `{$actualOutputHtml}`");

    $expectedOutputHtmlWithHtmlTags = "<html>{$expectedOutputHtml}</html>";
    $actualOutputHtmlWithHtmlTags = app(PersonalizeTextAction::class)->execute("<html>{$inputHtml}</html>", test()->send);

    expect($actualOutputHtmlWithHtmlTags)->toEqual($expectedOutputHtmlWithHtmlTags, "The personalize action did not produce the expected result when wrapped in html tags. Expected: `{$expectedOutputHtmlWithHtmlTags}`, actual: `{$actualOutputHtmlWithHtmlTags}`");
}
