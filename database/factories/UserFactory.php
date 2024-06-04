<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_name' => 'admin',
            'email' => 'sociablesphere@gmail.com',
            'name' => '',
            'last_name' => '',
            'photo' => null,
            'description' => null,
            'password' => bcrypt('1234'),
            'role' => 'admin',
            'wallet' => 0,
            'api_token' => Str::random(60),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
