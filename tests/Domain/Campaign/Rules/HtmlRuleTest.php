<?php

use Spatie\Mailcoach\Domain\Campaign\Rules\HtmlRule;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

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
    return (new HtmlRule())->passes('html', $html);
}
