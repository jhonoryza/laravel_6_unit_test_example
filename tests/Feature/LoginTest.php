<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class LoginTest extends TestCase
{
    protected $endpoint = 'api/login';

    /** @test */
    public function must_enter_email_and_password()
    {
        $this->postJson($this->endpoint)
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    'email' => ["The email field is required."],
                    'password' => ["The password field is required."],
                ]
            ]);
    }

    /** @test */
    public function failed_login()
    {
        $login = ['email' => 'doe@example.com', 'password' => 'test'];

        $this->postJson($this->endpoint, $login)
            ->assertStatus(200)
            ->assertJson([
                "message" => "Invalid Credentials",
            ]);
    }

    /** @test */
    public function successful_login()
    {
        $login = ['email' => 'doe@example.com', 'password' => 'aziz'];

        $this->postJson($this->endpoint, $login)
            ->assertStatus(200)
            ->assertJsonStructure([
               "user" => [
                   'id',
                   'name',
                   'email',
                   'email_verified_at',
                   'created_at',
                   'updated_at',
               ],
                "access_token",
                "message"
            ]);

        $this->assertAuthenticated();
    }
}
