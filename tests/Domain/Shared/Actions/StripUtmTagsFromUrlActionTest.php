<?php

namespace Spatie\Mailcoach\Tests\Domain\Shared\Actions;

use Spatie\Mailcoach\Domain\Shared\Actions\StripUtmTagsFromUrlAction;
use Spatie\Mailcoach\Tests\TestCase;

class StripUtmTagsFromUrlActionTest extends TestCase
{
    private StripUtmTagsFromUrlAction $action;

    public function setUp(): void
    {
        parent::setUp();

        $this->action = resolve(StripUtmTagsFromUrlAction::class);
    }

    /**
     * @test
     * @dataProvider provider
     *
     * @param string $url
     * @param string $urlWithTags
     */
    public function it_strips_utm_tags_from_an_url(string $url, string $urlWithoutTags)
    {
        $this->assertEquals($urlWithoutTags, $this->action->execute($url));
    }

    public function provider()
    {
        yield ['https://spatie.be?utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign', 'https://spatie.be'];
        yield ['https://spatie.be/?utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign', 'https://spatie.be/'];
        yield ['https://spatie.be/foo?utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign', 'https://spatie.be/foo'];
        yield ['https://spatie.be/foo/bar?utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign', 'https://spatie.be/foo/bar'];
        yield ['https://spatie.be?foo=bar&utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign', 'https://spatie.be?foo=bar'];
        yield ['https://spatie.be/foo/bar?foo=bar&utm_source=newsletter&utm_medium=email&utm_campaign=My+Campaign', 'https://spatie.be/foo/bar?foo=bar'];
    }
}
