<?php

namespace Tests\Feature;

use Tests\TestCase;

class RegistrationTest extends TestCase
{
    protected $endpoint = 'api/register';

    /** @test */
    public function required_field_for_registration()
    {
        return $this->postJson($this->endpoint)
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "name" => ["The name field is required."],
                    "email" => ["The email field is required."],
                    "password" => ["The password field is required."],
                ]
            ]);
    }

    /** @test */
    public function repeat_password()
    {
        $user = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "password" => "demo12345"
        ];

        return $this->postJson($this->endpoint, $user)
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "password" => ["The password confirmation does not match."]
                ]
            ]);
    }

    /** @test */
    public function success_registration()
    {
        $user = [
            "name" => "John Doe",
            "email" => "doe@example.com",
            "password" => "aziz",
            "password_confirmation" => "aziz"
        ];

        return $this->postJson($this->endpoint, $user)
            ->assertStatus(201)
            ->assertJsonStructure([
                "user" => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                "access_token",
                "message"
            ]);
    }
}
