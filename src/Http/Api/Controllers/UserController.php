<?php

namespace Spatie\Mailcoach\Http\Api\Controllers;

use Spatie\Mailcoach\Http\Api\Resources\UserResource;

class UserController
{
    public function __invoke()
    {
        return 'ok';

        return new UserResource(auth()->user());
    }
}
