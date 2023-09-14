<?php

namespace Tests\Feature\GraphQL\Cliente;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteMutationTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanCreateNewCliente()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        // Dados para a nova Cliente
        $title = 'Titulo Teste';

        // Consulta do GraphQL para criar um cliente
        $query = '
        mutation($title: String!){
            createCliente(title: $title) {
                id
            }
        }
        ';

        // Variáveis para a consulta
        $variables = [
            'title' => $title,
        ];

        // Realize a consulta GraphQL com as variáveis
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/graphql', [
            'query' => $query,
            'variables' => $variables,
        ]);

        // Verifique a resposta da consulta
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'createCliente' => [],
                ],
            ]);
    }
}
