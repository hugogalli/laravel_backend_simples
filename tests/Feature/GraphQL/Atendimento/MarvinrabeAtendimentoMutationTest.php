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

class MarvinrabeAtendimentoMutationTest extends TestCase
{
    use RefreshDatabase;

    public function testWithMarvinrabeUserCanCreateNewAtendimento()
    {
        $user = User::factory()->create(['type' => 'atendente']);
        $token = auth()->login($user);

        $this->withHeaders(["Authorization" => "Bearer {$token}"])->mutation(
            'createAtendimento',
            [
                'title' => 'Titulo Teste',
                'tipo' => 'problema',
                'description' => 'Descricao Teste',
                'pessoa' => 'Pessoa Teste',
                'cliente_id' => Cliente::factory()->create()->id,
                'area_id' => Area::factory()->create()->id,
            ],
            ['title', 'tipo', 'description', 'pessoa', 'cliente_id', 'area_id']
        )
            ->assertJsonFragment([
                'title' => 'Titulo Teste'
            ]);
    }
}
