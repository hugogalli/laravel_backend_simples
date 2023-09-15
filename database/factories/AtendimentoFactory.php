<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\Cliente;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class AtendimentoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $clienteIds = Cliente::pluck('id')->toArray();
        $areaIds = Area::pluck('id')->toArray();
        $userIds = User::where('type', 'atendente')->pluck('id')->toArray();

        return [
            'title' => $this->faker->word,
            'tipo' => $this->faker->randomElement(['problema', 'duvida', 'solicitacao']),
            'description' => $this->faker->sentence,
            'user_id' => $this->faker->randomElement($userIds)?:User::factory(),
            'pessoa' => $this->faker->name,
            'status' => 'pendente',
            'cliente_id' => $this->faker->randomElement($clienteIds)?:Cliente::factory(),
            'area_id' => $this->faker->randomElement($areaIds)?:Area::factory(),
            'analista_id' => null,
            'info_adicional' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
