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
        return [
            'title' => $this->faker->word,
            'tipo' => $this->faker->randomElement(['problema', 'duvida', 'solicitacao']),
            'description' => $this->faker->sentence,
            'user_id' => User::factory(),
            'pessoa' => $this->faker->name,
            'status' => 'pendente',
            'cliente_id' => Cliente::factory(),
            'area_id' => Area::factory(),
            'analista_id' => null,
            'info_adicional' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
