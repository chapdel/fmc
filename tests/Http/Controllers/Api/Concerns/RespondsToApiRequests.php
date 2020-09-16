<?php

namespace Spatie\Mailcoach\Tests\Http\Controllers\Api\Concerns;

use Database\Factories\UserFactory;

trait RespondsToApiRequests
{
    public function loginToApi()
    {
        $user = UserFactory::new()->create();

        $this->actingAs($user, 'api');
    }
}
