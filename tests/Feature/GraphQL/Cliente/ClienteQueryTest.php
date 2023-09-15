<?php

namespace Tests\Feature\GraphQL\Cliente;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteQueryTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanGetListOfClientes()
    {

        $user = User::factory()->create();
        $token = auth()->login($user);
        $cliente = Cliente::factory()->create();

        // Consulta do GraphQL
        $query = '
            query {
                clientes {
                    title
                    id
                }
            }
        ';

        // Pegando a consulta em um JSON
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/graphql', [
            'query' => $query,
            //'variables' => $variables,
        ]);

        // Verificando a resposta da consulta
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'clientes' => [
                        [
                            'title' => $cliente->title,
                            'id' => $cliente->id,
                        ],
                    ],
                ],
            ]);
    }

    public function testUserCanQueryClienteById()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $cliente = Cliente::factory()->create();

        // Consulta do GraphQL com o parâmetro de ID
        $query = '
        query ($id: Int!) {
            cliente(id: $id) {
                title
                id
            }
        }
    ';

        // Variáveis para a consulta
        $variables = [
            'id' => $cliente->id,
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
            ->assertJson([
                'data' => [
                    'cliente' => [
                        'title' => $cliente->title,
                        'id' => $cliente->id,
                    ],
                ],
            ]);
    }
}
