<?php

namespace Tests\Feature\GraphQL\Relations;

use App\Models\Area;
use App\Models\User;
use Tests\TestCase;

class RelationsTest extends TestCase
{
    public function testConnectAnalistaToArea()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        // Dados para a nova relation
        $analistaId = User::factory()->create(['type' => 'suporte'])->id;
        $areaId = Area::factory()->create()->id;

        // Consulta do GraphQL para criar uma relacao
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
            'analistaId' => $analistaId,
            'areaId' => $areaId,
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
                    'associateArea' => [],
                ],
            ]);
    }

    public function testDisconnectAnalistaToArea()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        // Dados para a nova relation
        $analista = User::factory()->create(['type' => 'suporte']);
        $analistaId = $analista->id;
        $areaId = Area::factory()->create()->id;
        $analista->areas()->attach($areaId);

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
            'analistaId' => $analistaId,
            'areaId' => $areaId,
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
                    'dissociateArea' => [],
                ],
            ]);
    }
}
