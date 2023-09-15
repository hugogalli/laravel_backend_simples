<?php

namespace Tests\Feature\GraphQL\Relations;

use App\Models\Area;
use App\Models\User;
use Exception;
use Tests\TestCase;

class RelationsTest extends TestCase
{
    public function testConnectAnalistaToArea()
    {
        $user = User::factory()->create(['type' => 'atendente']);
        $token = auth()->login($user);

        // Dados para a nova relação
        $analista = User::factory()->create(['type' => 'suporte']);
        $area = Area::factory()->create();

        // Consulta do GraphQL para criar uma relação
        $query = '
    mutation(
        $analistaId: Int!
        $areaId: Int!){
        associateArea(
            analistaId: $analistaId
            areaId: $areaId
        )
    }
    ';

        // Variáveis para a consulta
        $variables = [
            'analistaId' => $analista->id,
            'areaId' => $area->id,
        ];

        // Execute a consulta GraphQL com as variáveis
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/graphql', [
            'query' => $query,
            'variables' => $variables,
        ]);

        $response->assertJson([
            'data' => [
                'associateArea' => "Analista {$analista->name} associado com sucesso com area {$area->title}",
            ],
        ]);
    }


    public function testDisconnectAnalistaToArea()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        // Dados para a nova relation
        $analista = User::factory()->create(['type' => 'suporte']);
        $area = Area::factory()->create();
        $analista->areas()->attach($area->id);

        // Consulta do GraphQL para criar uma relacao
        $query = '
        mutation(
            $analistaId: Int!
            $areaId: Int!){
            dissociateArea(
                analistaId: $analistaId
                areaId: $areaId
                )
        }
        ';

        // Variáveis para a consulta
        $variables = [
            'analistaId' => $analista->id,
            'areaId' => $area->id,
        ];

        // Realize a consulta GraphQL com as variáveis
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/graphql', [
            'query' => $query,
            'variables' => $variables,
        ]);

        // Verifique a resposta da consulta
        $response->assertJson([
            'data' => [
                'dissociateArea' => "Analista {$analista->name} dessvinculado com sucesso da area {$area->title}",
            ],
        ]);
    }

    public function testConnectAtendenteToArea()
    {
        $user = User::factory()->create(['type' => 'atendente']);
        $token = auth()->login($user);

        // Dados para a nova relação
        $analista = User::factory()->create(['type' => 'atendente']);
        $area = Area::factory()->create();

        // Consulta do GraphQL para criar uma relação
        $query = '
        mutation(
            $analistaId: Int!
            $areaId: Int!){
            associateArea(
                analistaId: $analistaId
                areaId: $areaId
            )
        }
    ';

        $variables = [
            'analistaId' => $analista->id,
            'areaId' => $area->id,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/graphql', [
            'query' => $query,
            'variables' => $variables,
        ]);

        $response->assertJsonFragment(['debugMessage' => 'Somente usuarios do tipo suporte podem ser associados com areas']);
    }
}
