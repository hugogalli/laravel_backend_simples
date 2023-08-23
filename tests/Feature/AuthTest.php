<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_user_can_register()
    {
        $userData = [
            'name' => 'Teste PHPUnit',
            'email' => 'phpunitteste@example.com',
            'password' => 'teste123',
            'type' => 'atendente'
        ];

        $response = $this->postJson(route('register'), $userData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['email' => 'phpunitteste@example.com']);
    }

    public function test_user_cannot_register_invalid_type()
    {
        $userData = [
            'name' => 'Teste PHPUnit',
            'email' => 'phpunitteste@example.com',
            'password' => 'teste123',
            'type' => 'errado'
        ];

        $response = $this->postJson(route('register'), $userData);

        $response->assertStatus(422);
    }

    /** @test */
    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'phpunitteste@example.com',
            'password' => bcrypt('teste123'),
        ]);

        $response = $this->postJson(route('login'), [
            'email' => 'phpunitteste@example.com',
            'password' => 'teste123',
        ]);

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->postJson(route('logout'), [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $this->assertGuest();
    }

    public function test_user_can_refresh_token()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->postJson(route('refresh'), [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'user',
            'authorisation' => ['token', 'type'],
        ]);
    }

    /** @test */
    public function test_user_can_get_profile_by_id()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->getJson(route('user.getProfileById', ['id' => $user->id]), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
    }

    public function test_user_can_get_own_info()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->getJson(route('user.getMyProfile'), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
    }

    // PARA USUARIOS SEM O JWT  (a nao ser login e register)

    /** @test */
    public function test_unauthenticated_user_cannot_refresh_token()
    {
        $response = $this->postJson(route('refresh'));

        $response->assertStatus(401);
    }

    /** @test */
    public function test_unauthenticated_user_cannot_get_own_profile()
    {
        $user = User::factory()->create();

        $response = $this->getJson(route('user.getProfileById', ['id' => $user->id]));

        $response->assertStatus(401);
    }

    /** @test */
    public function test_unauthenticated_user_cannot_get_own_info()
    {
        $response = $this->getJson(route('user.getMyProfile'));

        $response->assertStatus(401);
    }
}
