<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 401 unauthenticated
 * 403 unauthorized
 * 404 not found
 * 201 created
 * 200 get, update, delete
 * 422 data invalid or validation error
 */
class ApiLoginTest extends TestCase
{
    use RefreshDatabase;
    private $endpoint = 'api/login';

    /** @test */
    public function login_test()
    {
        $user = factory(User::class)->create([
            'email' => 'admin@admin.com',
            'name' => 'admin'
        ]);
        $response = $this->postJson($this->endpoint, [
            'email' => $user->email,
            'password' => $user->password
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'success'
        ]);
        $response->assertJsonStructure([
            'data',
            'message',
            'token'
        ]);
    }

    /** @test */
    public function login_incorrect_credential_test()
    {
        $response = $this->postJson($this->endpoint, [
            'email' => 'asal@asal.com',
            'password' => 'password'
        ]);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors'
        ]);
    }
}
