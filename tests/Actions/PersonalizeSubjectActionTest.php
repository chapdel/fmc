<?php

namespace Spatie\Mailcoach\Tests\Actions;

use Spatie\Mailcoach\Domain\Campaign\Actions\PersonalizeSubjectAction;
use Spatie\Mailcoach\Database\Factories\CampaignSendFactory;
use Spatie\Mailcoach\Domain\Campaign\Models\Send;
use Spatie\Mailcoach\Tests\TestCase;

class PersonalizeSubjectActionTest extends TestCase
{
    /** @var \Spatie\Mailcoach\Domain\Campaign\Models\Send */
    private Send $send;

    /** @var \Spatie\Mailcoach\Domain\Campaign\Actions\PersonalizeSubjectAction */
    private PersonalizeSubjectAction $personalizeSubjectAction;

    public function setUp(): void
    {
        parent::setUp();

        $this->send = CampaignSendFactory::new()->create();

        $subscriber = $this->send->subscriber;
        $subscriber->uuid = 'my-uuid';
        $subscriber->extra_attributes = ['first_name' => 'John', 'last_name' => 'Doe'];
        $subscriber->save();

        $this->send->campaign->update(['name' => 'my campaign']);

        $this->personalizeSubjectAction = new PersonalizeSubjectAction();
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

    protected function assertActionResult(string $originalSubject, $expectedSubject)
    {
        $actualOutputHtml = (new PersonalizeSubjectAction())->execute($originalSubject, $this->send);
        $this->assertEquals($expectedSubject, $actualOutputHtml, "The personalize action did not produce the expected result. Expected: `{$expectedSubject}`, actual: `{$actualOutputHtml}`");

        $expectedOutputHtmlWithHtmlTags = "{$expectedSubject}";
        $actualOutputHtmlWithHtmlTags = (new PersonalizeSubjectAction())->execute("$originalSubject", $this->send);

        $this->assertEquals($expectedOutputHtmlWithHtmlTags, $actualOutputHtmlWithHtmlTags, "The personalize action did not produce the expected result when wrapped in html tags. Expected: `{$expectedOutputHtmlWithHtmlTags}`, actual: `{$actualOutputHtmlWithHtmlTags}`");
    }
}
