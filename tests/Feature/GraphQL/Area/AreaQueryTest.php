<?php

namespace Tests\Feature\GraphQL\Area;

use App\Models\Area;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AreaQueryTest extends TestCase
{

    use RefreshDatabase;

    public function testUserCanGetListOfAreas()
    {
        $user = User::factory()->create();
        $area = Area::factory()->create();
        $token = auth()->login($user);

        // Consulta do GraphQL
        $query = '
            query {
                areas {
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
                    'areas' => [
                        [
                            'id' => $area->id,
                        ],
                    ],
                ],
            ]);
    }

    public function testUserCanQueryAreaById()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $area = Area::factory()->create();

        // Consulta do GraphQL com o parâmetro de ID
        $query = '
        query ($id: Int!) {
            area(id: $id) {
                id
            }
        }
    ';

        // Variáveis para a consulta
        $variables = [
            'id' => $area->id,
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
                    'area' => [
                        'id' => $area->id,
                    ],
                ],
            ]);
    }
}
