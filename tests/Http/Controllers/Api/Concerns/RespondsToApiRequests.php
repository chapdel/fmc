<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns;

use Illuminate\Foundation\Auth\User;

trait RespondsToApiRequests
{
    public function loginToApi()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api');
    }
}
