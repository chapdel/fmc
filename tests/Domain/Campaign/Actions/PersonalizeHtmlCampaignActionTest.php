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

    test()->personalizeHtmlAction = app(PersonalizeTextAction::class);
});

it('can replace an placeholder for a subscriber attribute', function () {
    assertPersonalizeCampaignHtmlActionResult('::subscriber.uuid::', 'my-uuid');
});

it('will not replace a non existing attribute', function () {
    assertPersonalizeCampaignHtmlActionResult('::subscriber.non-existing::', '::subscriber.non-existing::');
});

it('can replace an placeholder for a subscriber extra attribute', function () {
    assertPersonalizeCampaignHtmlActionResult('::subscriber.extra_attributes.first_name::', 'John');
});

it('will not replace an placeholder for a non existing subscriber extra attribute', function () {
    assertPersonalizeCampaignHtmlActionResult('::subscriber.extra_attributes.non-existing::', '::subscriber.extra_attributes.non-existing::');
});

it('can replace unsubscribe url', function () {
    assertPersonalizeCampaignHtmlActionResult('::unsubscribeUrl::', test()->send->subscriber->unsubscribeUrl(test()->send));
});

it('can replace unsubscribe tag url', function () {
    test()->send->subscriber->addTag('some tag');

    assertPersonalizeCampaignHtmlActionResult('::unsubscribeTag::some tag::', test()->send->subscriber->unsubscribeTagUrl('some tag', test()->send));
});

it('can use twig templating for replacers', function () {
    assertPersonalizeCampaignHtmlActionResult('{{subscriber.uuid}}', 'my-uuid');
    assertPersonalizeCampaignHtmlActionResult('{{subscriber.first_name}}', 'John');
    assertPersonalizeCampaignHtmlActionResult('{{subscriber.extra_attributes.first_name}}', 'John');
    assertPersonalizeCampaignHtmlActionResult('{{subscriber.last_name}}', 'Doe');
    assertPersonalizeCampaignHtmlActionResult('{{subscriber.coupon}}', '');
    assertPersonalizeCampaignHtmlActionResult('{{ this_does_not_exist }}', '');
    assertPersonalizeCampaignHtmlActionResult('{{campaign.name}}', 'my campaign');
    assertPersonalizeCampaignHtmlActionResult('{% if subscriber.first_name %}Hello {{subscriber.first_name}}{% endif %}', 'Hello John');
    assertPersonalizeCampaignHtmlActionResult('{%if not subscriber.coupon %}No coupon{% endif %}', 'No coupon');
});

// Helpers
function assertPersonalizeCampaignHtmlActionResult(string $inputHtml, $expectedOutputHtml)
{
    $actualOutputHtml = app(PersonalizeTextAction::class)->execute($inputHtml, test()->send);
    test()->assertEquals($expectedOutputHtml, $actualOutputHtml, "The personalize action did not produce the expected result. Expected: `{$expectedOutputHtml}`, actual: `{$actualOutputHtml}`");

    $expectedOutputHtmlWithHtmlTags = "<html>{$expectedOutputHtml}</html>";
    $actualOutputHtmlWithHtmlTags = app(PersonalizeTextAction::class)->execute("<html>{$inputHtml}</html>", test()->send);

    test()->assertEquals($expectedOutputHtmlWithHtmlTags, $actualOutputHtmlWithHtmlTags, "The personalize action did not produce the expected result when wrapped in html tags. Expected: `{$expectedOutputHtmlWithHtmlTags}`, actual: `{$actualOutputHtmlWithHtmlTags}`");
}
