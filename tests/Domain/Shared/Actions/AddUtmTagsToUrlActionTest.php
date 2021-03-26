<?php

namespace Spatie\Mailcoach\Tests\Domain\Shared\Actions;

use Spatie\Mailcoach\Domain\Shared\Actions\AddUtmTagsToUrlAction;
use Spatie\Mailcoach\Tests\TestCase;

class AddUtmTagsToUrlActionTest extends TestCase
{
    private AddUtmTagsToUrlAction $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->action = resolve(AddUtmTagsToUrlAction::class);
    }

    /**
     * @test
     * @dataProvider provider
     *
     * @param string $url
     * @param string $urlWithTags
     */
    public function it_adds_utm_tags_to_an_url(string $url, string $urlWithTags)
    {
        $this->assertEquals($urlWithTags, $this->action->execute($url, 'My Campaign'));
    }

    public function provider()
    {
        yield ['https://spatie.be', 'https://spatie.be?utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign'];
        yield ['https://spatie.be/', 'https://spatie.be/?utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign'];
        yield ['https://spatie.be/foo', 'https://spatie.be/foo?utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign'];
        yield ['https://spatie.be/foo/bar', 'https://spatie.be/foo/bar?utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign'];
        yield ['https://spatie.be?foo=bar', 'https://spatie.be?foo=bar&utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign'];
        yield ['https://spatie.be/foo/bar?foo=bar', 'https://spatie.be/foo/bar?foo=bar&utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign'];
        yield ['mailto:info@spatie.be', 'mailto:info@spatie.be'];
        yield ['tel:info@spatie.be', 'tel:info@spatie.be'];
        yield ['anything-else', 'anything-else'];
    }
}
