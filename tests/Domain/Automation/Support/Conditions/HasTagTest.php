<?php

namespace Spatie\Mailcoach\Tests\Domain\Automation\Support\Conditions;

use Spatie\Mailcoach\Domain\Audience\Models\Subscriber;
use Spatie\Mailcoach\Domain\Automation\Support\Conditions\HasTagCondition;
use Spatie\Mailcoach\Tests\TestCase;

class HasTagTest extends TestCase
{
    /** @test * */
    public function it_checks_for_a_tag()
    {
        $subscriber = Subscriber::factory()->create();

        $condition = new HasTagCondition($subscriber, [
            'tag' => 'some-tag',
        ]);

        $this->assertFalse($condition->check());

        $subscriber->addTag('some-tag');

        $this->assertTrue($condition->check());
    }
}
