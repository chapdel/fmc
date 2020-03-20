<?php

namespace Spatie\Mailcoach\Tests\Support;

use Spatie\Mailcoach\Models\Subscriber;
use Spatie\Mailcoach\Support\Replacers\Concerns\ReplacesModelAttributes;
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
        $subscriber = factory(Subscriber::class)->create([
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
        $subscriber = factory(Subscriber::class)->create();

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
        $subscriber = factory(Subscriber::class)->create();

        $output = $this->classWithTrait->replaceModelAttributes(
            "This is ::subscriber.non_existing_attribute::",
            'subscriber',
            $subscriber
        );

        $this->assertEquals("This is ", $output);
    }
}
