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

class MarvinrabeAtendimentoQueryTest extends TestCase
{
    use RefreshDatabase;

    public function testWithMarvinrabeUserCanGetListOfAtendimentos()
    {
        $user = User::factory()->create(['type' => 'atendente']);
        $token = auth()->login($user);

        $atendimento = Atendimento::factory()->create();

        $this->withHeaders(["Authorization" => "Bearer {$token}"])->query(
            'atendimentos',
            ['id', 'title', 'tipo', 'description', 'pessoa', 'cliente_id', 'area_id']
        )
            ->assertJsonFragment([
                'id' => $atendimento->id,
                'title' => $atendimento->title,
                'tipo' => $atendimento->tipo,
                'description' => $atendimento->description,
                'pessoa' => $atendimento->pessoa,
                'cliente_id' => $atendimento->cliente_id,
                'area_id' => $atendimento->area_id,
            ]);
    }
}
