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

class AtendimentoMutationTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanCreateNewAtendimento()
    {
        $user = User::factory()->create(['type' => 'atendente']);
        $token = auth()->login($user);

        // Dados para a nova area
        $title = 'Titulo Teste';

        $tipos = ['problema', 'solicitacao', 'duvida'];
        $indiceAleatorio = array_rand($tipos);
        $tipo = $tipos[$indiceAleatorio];

        $description = 'Descricao Teste';
        $pessoa = 'John Doe';

        $cliente_id = Cliente::factory()->create()->id;
        $area_id = Area::factory()->create()->id;

        // Consulta do GraphQL para criar um novo atendimento
        $query = '
        mutation(
            $title: String!
            $tipo: String!
            $description: String!
            $pessoa: String!
            $cliente_id: Int!
            $area_id: Int!

            ){
            createAtendimento(
                title: $title
                tipo: $tipo
                description: $description
                pessoa: $pessoa
                cliente_id: $cliente_id
                area_id: $area_id
                ) {
                id
            }
        }
        ';

        // Variáveis para a consulta
        $variables = [
            'title' => $title,
            'tipo' => $tipo,
            'description' => $description,
            'pessoa' => $pessoa,
            'cliente_id' => $cliente_id,
            'area_id' => $area_id,
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
                    'createAtendimento' => [
                        'id',
                    ],
                ],
            ]);
    }
}
