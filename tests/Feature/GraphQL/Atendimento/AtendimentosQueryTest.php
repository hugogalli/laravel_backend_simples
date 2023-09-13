<?php

namespace Tests\Feature\GraphQL\Atendimento;

use App\Models\Atendimento;
use App\Models\User;
use Tests\TestCase;

class AtendimentosQueryTest extends TestCase
{

    public function testUserCanGetListOfAtendimentos()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        // Consulta do GraphQL
        $query = '
            query {
                atendimentos {
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
            ->assertJsonStructure([
                'data' => [
                    'atendimentos' => [
                        '*' => [],
                    ],
                ],
            ]);
    }

    public function testUserCanQueryAtendimentoById()
    {
        // ID do atendimento que você deseja consultar
        $user = User::factory()->create();
        $token = auth()->login($user);

        $atendimento = Atendimento::factory()->create();

        // Consulta do GraphQL com o parâmetro de ID
        $query = '
        query ($id: Int!) {
            atendimento(id: $id) {
                id
            }
        }
    ';

        // Variáveis para a consulta
        $variables = [
            'id' => $atendimento->id,
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
                    'atendimento' => [],
                ],
            ]);
    }
}
