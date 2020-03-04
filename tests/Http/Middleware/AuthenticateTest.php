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
    public function when_not_authenticated_it_redirects_to_the_login_route()
    {
        $this->get(route('mailcoach.campaigns'))->assertRedirect(route('login'));
    }

    /** @test */
    public function when_authenticated_it_can_view_the_mailcoach_ui()
    {
        $this->withoutExceptionHandling();

        $this->authenticate();

        $this->get(route('mailcoach.campaigns'))->assertSuccessful();
    }

    /** @test */
    public function it_will_redirect_to_the_login_page_when_authenticated_with_the_wrong_guard()
    {
        config()->set('mailcoach.guard', 'api');

        $this->authenticate('web');

        $this->get(route('mailcoach.campaigns'))->assertRedirect(route('login'));
    }

    /** @test */
    public function when_authenticated_with_the_right_guard_it_can_view_the_mailcoach_ui()
    {
        config()->set('mailcoach.guard', 'api');

        $this->authenticate('api');

        $this->get(route('mailcoach.campaigns'))->assertSuccessful();
    }
}
