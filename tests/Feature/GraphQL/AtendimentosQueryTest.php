<?php

namespace Tests\Feature\GraphQL;

use App\Models\Area;
use App\Models\Atendimento;
use App\Models\Cliente;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        $response = $this->postJson('/graphql', ['query' => $query]);

        // Verificando a resposta da consulta
        $response->assertStatus(200)
        ->assertJsonStructure([
        'data' => [
            'atendimentos' => [
                '*' => [
                    'id',
                ],
            ],
        ],
        ]);
    }

    public function testUserCanQueryAtendimentoById()
{
    // ID do atendimento que você deseja consultar
    $atendimentoId = 1; // Substitua pelo ID desejado

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
        'id' => $atendimentoId,
    ];

    // Realize a consulta GraphQL com as variáveis
    $response = $this->postJson('/graphql', [
        'query' => $query,
        'variables' => $variables,
    ]);

    // Verifique a resposta da consulta
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'atendimento' => [
                    'id',
                ],
            ],
        ]);
}


}