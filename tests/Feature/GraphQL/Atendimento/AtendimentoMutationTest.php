<?php

namespace Tests\Feature\GraphQL\Atendimento;

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
                title
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
        $response->assertJsonFragment(['title' => 'Titulo Teste']);
    }

    public function testUserCanDeleteAtendimento()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);
        $atendimento = Atendimento::factory()->create();

        // Id para deletarmos
        $atendimento_id = $atendimento->id;

        // Consulta do GraphQL para chamar a mutation de deletar
        $query = '
        mutation($atendimento_id: Int!){
            deleteAtendimento(atendimento_id: $atendimento_id)
        }
        ';

        // Variáveis para a consulta
        $variables = [
            'atendimento_id' => $atendimento_id,
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
                    'deleteAtendimento' => [],
                ],
            ]);
    }

    public function testUserCanConcluirAtendimento()
    {
        $user = User::factory()->create(['type' => 'suporte']);
        $token = auth()->login($user);
        $area_id = Area::factory()->create()->id;
        $user->areas()->attach($area_id);
        $atendimento = Atendimento::factory()->create(['area_id' => $area_id]);
        $atendimento_id = $atendimento->id;

        $info_adicional = 'Info Adicional';

        $query = '
        mutation(
            $atendimento_id: Int!
            $info_adicional: String!
            ){
            concluirAtendimento(
                atendimento_id: $atendimento_id
                info_adicional: $info_adicional
                )
        }
        ';

        // Variáveis para a consulta
        $variables = [
            'atendimento_id' => $atendimento_id,
            'info_adicional' => $info_adicional,
        ];

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
                    'concluirAtendimento' => [],
                ],
            ]);
    }

    public function testUserCanTomarPosseDeAtendimento()
    {
    }

    public function testUserCanTransferirPosseDeAtendimento()
    {
    }
}
