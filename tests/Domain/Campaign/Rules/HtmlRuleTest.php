<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Rules;

use Spatie\Mailcoach\Domain\Campaign\Rules\HtmlRule;
use Spatie\Mailcoach\Tests\TestCase;

class HtmlRuleTest extends TestCase
{
    /** @test */
    public function it_passes_if_the_html_is_valid()
    {
        $this->assertTrue($this->rulePasses('<html><body>Test</body></html>'));

        $this->assertFalse($this->rulePasses('<html>>><html>'));
    }

    /** @test * */
    public function it_passes_with_ampersands()
    {
        $this->assertTrue($this->rulePasses('<html><a href="https://google.com?foo=true&bar=false">Test</a></html>'));
    }

    protected function rulePasses(string $html)
    {
        return (new HtmlRule())->passes('html', $html);
    }
}
