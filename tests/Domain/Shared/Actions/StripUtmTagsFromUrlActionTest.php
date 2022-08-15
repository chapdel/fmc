<?php

use Spatie\Mailcoach\Domain\Shared\Actions\StripUtmTagsFromUrlAction;

beforeEach(function () {
    test()->action = resolve(StripUtmTagsFromUrlAction::class);
});

/**
 * @param  string  $url
 * @param  string  $urlWithTags
 */
it('strips utm tags from an url', function (string $url, string $urlWithoutTags) {
    expect(test()->action->execute($url))->toEqual($urlWithoutTags);
})->with('provider');

// Datasets
dataset('provider', function () {
    yield ['https://spatie.be', 'https://spatie.be'];
    yield ['https://spatie.be?utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign', 'https://spatie.be'];
    yield ['https://spatie.be/?utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign', 'https://spatie.be/'];
    yield ['https://spatie.be/foo?utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign', 'https://spatie.be/foo'];
    yield ['https://spatie.be/foo/bar?utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign', 'https://spatie.be/foo/bar'];
    yield ['https://spatie.be?foo=bar&utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign', 'https://spatie.be?foo=bar'];
    yield ['https://spatie.be/foo/bar?foo=bar&utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign', 'https://spatie.be/foo/bar?foo=bar'];
});
