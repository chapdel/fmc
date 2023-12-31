<?php

use Spatie\Mailcoach\Domain\Campaign\Rules\HtmlRule;

it('passes if the html is valid', function () {
    expect(rulePasses('<html><body>Test</body></html>'))->toBeTrue();

    expect(rulePasses('<html>>><html>'))->toBeFalse();
});

it('passes with ampersands', function () {
    expect(rulePasses('<html><a href="https://google.com?foo=true&bar=false">Test</a></html>'))->toBeTrue();
});

// Helpers
function rulePasses(string $html)
{
    $passed = true;
    (new HtmlRule())->validate('html', $html, function () use (&$passed) {
        $passed = false;
    });

    return $passed;
}
