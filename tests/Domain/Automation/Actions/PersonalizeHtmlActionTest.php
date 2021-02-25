<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Actions;

use Spatie\Mailcoach\Database\Factories\SendFactory;
use Spatie\Mailcoach\Domain\Automation\Actions\PersonalizeHtmlAction;
use Spatie\Mailcoach\Domain\Shared\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;

class PersonalizeHtmlActionTest extends TestCase
{
    /** @var \Spatie\Mailcoach\Domain\Shared\Models\Send */
    protected Send $send;

    /** @var \Spatie\Mailcoach\Domain\Automation\Actions\PersonalizeHtmlAction */
    protected PersonalizeHtmlAction $personalizeHtmlAction;

    public function setUp(): void
    {
        parent::setUp();

        $this->send = SendFactory::new()->create();

        $subscriber = $this->send->subscriber;
        $subscriber->uuid = 'my-uuid';
        $subscriber->extra_attributes = ['first_name' => 'John', 'last_name' => 'Doe'];
        $subscriber->save();

        $this->send->campaign->update(['name' => 'my campaign']);

        $this->personalizeHtmlAction = new PersonalizeHtmlAction();
    }

    /** @test */
    public function it_can_replace_an_placeholder_for_a_subscriber_attribute()
    {
        $this->assertActionResult('::subscriber.uuid::', 'my-uuid');
    }

    /** @test */
    public function it_will_not_replace_a_non_existing_attribute()
    {
        $this->assertActionResult('::subscriber.non-existing::', '::subscriber.non-existing::');
    }

    /** @test */
    public function it_can_replace_an_placeholder_for_a_subscriber_extra_attribute()
    {
        $this->assertActionResult('::subscriber.extra_attributes.first_name::', 'John');
    }

    /** @test */
    public function it_will_not_replace_an_placeholder_for_a_non_existing_subscriber_extra_attribute()
    {
        $this->assertActionResult('::subscriber.extra_attributes.non-existing::', '::subscriber.extra_attributes.non-existing::');
    }

    /** @test */
    public function it_can_replace_unsubscribe_url()
    {
        $this->assertActionResult('::unsubscribeUrl::', $this->send->subscriber->unsubscribeUrl($this->send));
    }

    /** @test */
    public function it_can_replace_unsubscribe_tag_url()
    {
        $this->send->subscriber->addTag('some tag');

        $this->assertActionResult('::unsubscribeTag::some tag::', $this->send->subscriber->unsubscribeTagUrl('some tag'));
    }

    protected function assertActionResult(string $inputHtml, $expectedOutputHtml)
    {
        $actualOutputHtml = (new PersonalizeHtmlAction())->execute($inputHtml, $this->send);
        $this->assertEquals($expectedOutputHtml, $actualOutputHtml, "The personalize action did not produce the expected result. Expected: `{$expectedOutputHtml}`, actual: `{$actualOutputHtml}`");

        $expectedOutputHtmlWithHtmlTags = "<html>{$expectedOutputHtml}</html>";
        $actualOutputHtmlWithHtmlTags = (new PersonalizeHtmlAction())->execute("<html>{$inputHtml}</html>", $this->send);

        $this->assertEquals($expectedOutputHtmlWithHtmlTags, $actualOutputHtmlWithHtmlTags, "The personalize action did not produce the expected result when wrapped in html tags. Expected: `{$expectedOutputHtmlWithHtmlTags}`, actual: `{$actualOutputHtmlWithHtmlTags}`");
    }
}
