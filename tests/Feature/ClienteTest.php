<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_user_can_get_list_of_clientes()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->getJson(route('clientes.index'), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'clientes' => [
                    '*' => ['id', 'title', 'created_at', 'updated_at'],
                ],
            ]);
    }

    /** @test */
    public function test_user_can_create_cliente()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $clienteData = [
            'title' => 'Test Cliente',
        ];

        $response = $this->postJson(route('cliente.store'), $clienteData, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'cliente created successfully',
                'cliente' => [
                    'title' => 'Test Cliente',
                ],
            ]);

        $this->assertDatabaseHas('clientes', [
            'title' => 'Test Cliente',
        ]);
    }

    /** @test */
    public function test_user_can_show_cliente()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $cliente = Cliente::factory()->create();

        $response = $this->getJson(route('cliente.show', ['id' => $cliente->id]), [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'cliente' => [
                    'title' => $cliente->title,
                ],
            ]);
    }

    /** @test */
    public function test_user_can_update_cliente()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $cliente = Cliente::factory()->create();

        $updatedData = [
            'title' => 'Updated Cliente',
        ];

        $response = $this->putJson(route('cliente.update', ['id' => $cliente->id]), $updatedData, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Cliente updated successfully',
                'cliente' => [
                    'title' => 'Updated Cliente',
                ],
            ]);

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'title' => 'Updated Cliente',
        ]);
    }

    /** @test */
    public function test_user_can_delete_cliente()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $cliente = Cliente::factory()->create();

        $response = $this->deleteJson(route('cliente.destroy', ['id' => $cliente->id]));
        $response->assertStatus(200);

        $this->assertDatabaseMissing('clientes', ['id' => $cliente->id]);
    }

    // Testes para usuários sem JWT TOKEN

    /** @test */
    public function test_guest_cannot_get_list_of_clientes()
    {
        $response = $this->getJson(route('clientes.index'));
        $response->assertStatus(401);
    }

    /** @test */
    public function test_guest_cannot_create_cliente()
    {
        $clienteData = [
            'title' => 'New Cliente',
        ];

        $response = $this->postJson(route('cliente.store'), $clienteData);
        $response->assertStatus(401);
    }

    /** @test */
    public function test_guest_cannot_show_cliente()
    {
        $cliente = Cliente::factory()->create();
        $response = $this->getJson(route('cliente.show', ['id' => $cliente->id]));
        $response->assertStatus(401);
    }

    /** @test */
    public function test_guest_cannot_update_cliente()
    {
        $cliente = Cliente::factory()->create();
        $response = $this->putJson(route('cliente.update', ['id' => $cliente->id]), [
            'title' => 'Updated Title',
        ]);
        $response->assertStatus(401);
    }

    /** @test */
    public function test_guest_cannot_delete_cliente()
    {
        $cliente = Cliente::factory()->create();
        $response = $this->deleteJson(route('cliente.destroy', ['id' => $cliente->id]));
        $response->assertStatus(401);
    }
}
