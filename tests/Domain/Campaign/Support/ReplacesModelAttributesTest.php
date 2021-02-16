<?php

namespace Spatie\Mailcoach\Tests\Domain\Campaign\Support;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Campaign\Support\Replacers\Concerns\ReplacesModelAttributes;
use Spatie\Mailcoach\Tests\Factories\SubscriberFactory;
use Spatie\Mailcoach\Tests\TestCase;

class ReplacesModelAttributesTest extends TestCase
{
    private ?object $classWithTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->classWithTrait = new class {
            use ReplacesModelAttributes;
        };
    }

    /** @test */
    public function it_can_replace_model_attributes()
    {
        $subscriber = Subscriber::factory()->create([
            'first_name' => 'John',
        ]);

        $output = $this->classWithTrait->replaceModelAttributes(
            "This is ::subscriber.first_name::",
            'subscriber',
            $subscriber
        );

        $this->assertEquals("This is John", $output);
    }

    /** @test */
    public function it_will_not_thrown_an_exception_when_trying_to_replace_an_attribute_with_a_null_value()
    {
        $subscriber = Subscriber::factory()->create();

        $output = $this->classWithTrait->replaceModelAttributes(
            "This is ::subscriber.first_name::",
            'subscriber',
            $subscriber
        );

        $this->assertEquals("This is ", $output);
    }

    /** @test */
    public function it_will_not_thrown_an_exception_when_trying_to_replace_a_non_existing_attribute()
    {
        $subscriber = Subscriber::factory()->create();

        $output = $this->classWithTrait->replaceModelAttributes(
            "This is ::subscriber.non_existing_attribute::",
            'subscriber',
            $subscriber
        );

        $this->assertEquals("This is ", $output);
    }

    /** @test */
    public function it_will_not_thrown_an_exception_when_trying_to_replace_a_non_existing_schemaless_attribute()
    {
        $subscriber = SubscriberFactory::new()->create();

        $output = $this->classWithTrait->replaceModelAttributes(
            "This is ::subscriber.extra_attributes.non_existing_attribute::",
            'subscriber',
            $subscriber
        );

        $this->assertEquals("This is ", $output);
    }
}
