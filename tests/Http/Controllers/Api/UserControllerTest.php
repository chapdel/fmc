<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api;

use Illuminate\Foundation\Auth\User;
use Spatie\Mailcoach\Tests\TestCase;

class UserControllerTest extends TestCase
{
    /** @test */
    public function it_can_detect_the_currently_logged_in_user()
    {
        $user = factory(User::class)->create();

        //$this->actingAs($user, 'api');

        $this
            ->get('test')
            ->assertSuccessful()
            ->assertJsonFragment([
                'name' => $user->name,
            ]);
    }
}
