<?php

use Spatie\Mailcoach\Domain\Campaign\Rules\HtmlRule;
use Spatie\Mailcoach\Tests\TestCase;

uses(TestCase::class);

it('passes if the html is valid', function () {
    test()->assertTrue(rulePasses('<html><body>Test</body></html>'));

    test()->assertFalse(rulePasses('<html>>><html>'));
});

it('passes with ampersands', function () {
    test()->assertTrue(rulePasses('<html><a href="https://google.com?foo=true&bar=false">Test</a></html>'));
});

// Helpers
function rulePasses(string $html)
{
    return (new HtmlRule())->passes('html', $html);
}
