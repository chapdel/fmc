<?php

namespace Spatie\Mailcoach\Tests\Domain\Shared;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Spatie\Mailcoach\Tests\TestCase;

class ToMailcoachFormatTest extends TestCase
{
    /** @test */
    public function it_registers_a_macro_on_the_date_facade()
    {
        Date::setTestNow('2020-08-12 09:17');

        $this->assertEquals('2020-08-12 09:17', Date::now()->toMailcoachFormat());
        $this->assertEquals('2020-08-12 09:17', Carbon::now()->toMailcoachFormat());
    }

    /** @test * */
    public function it_transforms_the_date_to_the_app_timezone()
    {
        config()->set('app.timezone', 'Europe/Brussels');

        Date::setTestNow('2020-08-12 09:17');

        $this->assertEquals('2020-08-12 11:17', Date::now()->toMailcoachFormat());
        $this->assertEquals('2020-08-12 11:17', Carbon::now()->toMailcoachFormat());
    }
}
