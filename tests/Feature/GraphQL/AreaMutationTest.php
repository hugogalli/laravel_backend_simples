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

class AreaMutationTest extends TestCase
{
    public function testUserCanCreateNewAtendimento()
    {   
        $user = User::factory()->create(['type' => 'atendente']);
        $token = auth()->login($user);

        // Dados para a nova area
        $title = 'Título do Atendimento';


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
        $response = $this->postJson('/graphql', [
            'query' => $query,
            'variables' => $variables,
        ]);

        // Verifique a resposta da consulta
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'createArea' => [
                        'id',
                    ],
                ],
            ]);
    }
}
