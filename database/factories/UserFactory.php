<?php

namespace Database\Factories;

use Illuminate\Foundation\Auth\User;
use \Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'email' => $this->faker->email,
        ];
    }
}
