<?php

namespace Tests\Feature\GraphQL\Area;

use App\Models\Area;
use App\Models\Atendimento;
use App\Models\Cliente;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
//use Faker\Factory as FakerFactory;

class AreaMutationTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanCreateNewArea()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        // Dados para a nova area
        $title = 'Titulo Teste';

        // Consulta do GraphQL para criar um novo atendimento
        $query = '
        mutation($title: String!){
            createArea(title: $title) {
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
                    'createArea' => [],
                ],
            ]);
    }

    public function testUserCanUpgradeArea()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);
        $area = Area::factory()->create();

        // Info para upgrade
        $id = $area->id;
        $title = 'Novo Titulo';

        // Consulta do GraphQL para chamar a mutation
        $query = '
        mutation(
            $id: Int!
            $title: String!
            ){
            updateArea(
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
                    'updateArea' => [],
                ],
            ]);
    }

    public function testUserCanDeleteArea()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);
        $area = Area::factory()->create();

        // Id para deletarmos
        $id = $area->id;

        // Consulta do GraphQL para chamar a mutation de deletar
        $query = '
        mutation($id: Int!){
            deleteArea(id: $id)
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
                    'deleteArea' => [],
                ],
            ]);
    }
}
