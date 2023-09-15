<?php

namespace Tests\Feature\GraphQL\Cliente;

use App\Models\Cliente;
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

    public function testUserCanUpgradeCliente()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);
        $cliente = Cliente::factory()->create();

        // Info para upgrade
        $id = $cliente->id;
        $title = 'Novo Titulo';

        // Consulta do GraphQL para chamar a mutation
        $query = '
        mutation(
            $id: Int!
            $title: String!
            ){
            updateCliente(
                id: $id
                title: $title
                )
        }
        ';

        // Variáveis para a consulta
        $variables = [
            'id' => $id,
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
                    'updateCliente' => [],
                ],
            ]);
    }

    public function testUserCanDeleteCliente()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);
        $cliente = Cliente::factory()->create();

        // Id para deletarmos
        $id = $cliente->id;

        // Consulta do GraphQL para chamar a mutation de deletar
        $query = '
        mutation($id: Int!){
            deleteCliente(id: $id)
        }
        ';

        // Variáveis para a consulta
        $variables = [
            'id' => $id,
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
                    'deleteCliente' => [],
                ],
            ]);
    }
}
