<?php

use Spatie\Mailcoach\Domain\Content\Actions\AddUtmTagsToUrlAction;

beforeEach(function () {
    test()->action = resolve(AddUtmTagsToUrlAction::class);
});

/**
 * @param  string  $url
 * @param  string  $urlWithTags
 */
it('adds utm tags to an url', function (string $url, string $urlWithTags) {
    expect(test()->action->execute($url, 'My Campaign'))->toEqual($urlWithTags);
})->with('urlProvider');

// Datasets
dataset('urlProvider', function () {
    yield ['https://spatie.be', 'https://spatie.be?utm_source=newsletter&utm_medium=email&utm_campaign=my-campaign'];
    yield ['https://spatie.be/', 'https://spatie.be/?utm_source=newsletter&utm_medium=email&utm_campaign=my-campaign'];
    yield ['https://spatie.be/foo', 'https://spatie.be/foo?utm_source=newsletter&utm_medium=email&utm_campaign=my-campaign'];
    yield ['https://spatie.be/foo/bar', 'https://spatie.be/foo/bar?utm_source=newsletter&utm_medium=email&utm_campaign=my-campaign'];
    yield ['https://spatie.be?foo=bar', 'https://spatie.be?foo=bar&utm_source=newsletter&utm_medium=email&utm_campaign=my-campaign'];
    yield ['https://spatie.be/foo/bar?foo=bar', 'https://spatie.be/foo/bar?foo=bar&utm_source=newsletter&utm_medium=email&utm_campaign=my-campaign'];
    yield ['https://spatie.be/#[data-service-uuid=\'1234\']', 'https://spatie.be/?utm_source=newsletter&utm_medium=email&utm_campaign=my-campaign#[data-service-uuid=\'1234\']'];
    yield ['https://spatie.be/test#newsletter', 'https://spatie.be/test?utm_source=newsletter&utm_medium=email&utm_campaign=my-campaign#newsletter'];
    yield ['mailto:info@spatie.be', 'mailto:info@spatie.be'];
    yield ['tel:info@spatie.be', 'tel:info@spatie.be'];
    yield ['anything-else', 'anything-else'];
});
