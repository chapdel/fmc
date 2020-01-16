<?php

namespace Spatie\Mailcoach\Tests\Rules;

use Spatie\Mailcoach\Rules\HtmlRule;
use Spatie\Mailcoach\Tests\TestCase;

class HtmlRuleTest extends TestCase
{
    /** @test */
    public function it_passes_if_the_html_is_valid()
    {
        $this->assertTrue($this->rulePasses('<html><body>Test</body></html>'));

        $this->assertFalse($this->rulePasses('<html>>><html>'));
    }

    protected function rulePasses(string $html)
    {
        return (new HtmlRule())->passes('html', $html);
    }
}
