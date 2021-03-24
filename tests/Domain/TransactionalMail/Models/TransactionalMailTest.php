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

    /** @test */
    public function it_can_group_clicks_per_url()
    {
        /** @var \Spatie\Mailcoach\Domain\TransactionalMail\Models\TransactionalMail $transactionalMail */
        $transactionalMail = TransactionalMailFactory::new()
            ->withClick(['url' => 'https://spatie.be'], 2)
            ->withClick(['url' => 'https://mailcoach.app'], 3)
            ->create();

        $this->assertCount(5, $transactionalMail->clicks);

        $groupedPerUrl = $transactionalMail->clicksPerUrl();

        $this->assertEquals($groupedPerUrl[0]['url'], 'https://mailcoach.app');
        $this->assertEquals($groupedPerUrl[0]['count'], 3);

        $this->assertEquals($groupedPerUrl[1]['url'], 'https://spatie.be');
        $this->assertEquals($groupedPerUrl[1]['count'], 2);
    }
}
