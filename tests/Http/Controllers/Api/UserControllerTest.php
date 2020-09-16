<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api;

use Spatie\Mailcoach\Http\Api\Controllers\UserController;
use Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RespondsToApiRequests;

    /** @test */
    public function it_can_detect_the_currently_logged_in_user()
    {
        $this->loginToApi();

        $this
            ->getJson(action(UserController::class))
            ->assertSuccessful()
            ->assertJsonFragment([
                'email' => auth()->user()->email,
            ]);
    }
}
