<?php

namespace Spatie\Mailcoach\Tests\Http\Middleware;

use Illuminate\Support\Facades\Route;
use Spatie\Mailcoach\Tests\TestCase;

class AuthenticateTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Route::get('login')->name('login');

        $this->withExceptionHandling();
    }

    /** @test */
    public function it_can_use_the_default_guard()
    {
        $this->get(route('mailcoach.campaigns'))->assertRedirect(route('login'));
    }

    /** @test */
    public function the_default_behaviour_works()
    {
        $this->authenticate();

        $this->get(route('mailcoach.campaigns'))->assertSuccessful();
    }

    /** @test */
    public function it_will_fail_when_authentication_with_a_wrong_guard()
    {
        config()->set('mailcoach.guard', 'api');

        $this->authenticate('web');

        $this->get(route('mailcoach.campaigns'))->assertRedirect(route('login'));
    }

    /** @test */
    public function it_can_authenticate_using_the_guard_specified_in_the_config_file()
    {
        config()->set('mailcoach.guard', 'api');

        $this->authenticate('api');

        $this->get(route('mailcoach.campaigns'))->assertSuccessful();
    }
}
