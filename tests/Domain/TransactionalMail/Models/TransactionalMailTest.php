<?php

namespace Spatie\Mailcoach\Tests\Domain\TransactionalMail\Models;

use Spatie\Mailcoach\Tests\Factories\TransactionalMailFactory;
use Spatie\Mailcoach\Tests\TestCase;

class TransactionalMailTest extends TestCase
{
    /** @test */
    public function the_open_relation_works()
    {
        $transactionalMailWithoutOpen = TransactionalMailFactory::new()->create();

        $transactionalMailWithOpen = TransactionalMailFactory::new()
            ->withOpen()
            ->create();

        $this->assertCount(0, $transactionalMailWithoutOpen->opens);
        $this->assertCount(1, $transactionalMailWithOpen->opens);
    }

    /** @test */
    public function the_click_relation_works()
    {
        $transactionalMailWithoutClick = TransactionalMailFactory::new()->create();

        $transactionalMailWithClick = TransactionalMailFactory::new()
            ->withClick()
            ->create();

        $this->assertCount(0, $transactionalMailWithoutClick->clicks);
        $this->assertCount(1, $transactionalMailWithClick->clicks);
    }
}
