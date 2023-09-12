<?php

namespace App\GraphQL\Mutations\Atendimento;

use App\Models\Atendimento;
use App\Models\User;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;

class TomarPosseMutation extends Mutation
{
    protected $attributes = [
        'title' => 'tomarPosse',
        'description' => 'Analista toma posse de Atendimento'
    ];

    public function type(): Type
    {
        return Type::string();
    }

    public function args(): array
    {
        return [
            'atendimento_id' => [
                'name' => 'atendimento_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required'],
            ],
        ];
    }

    public function resolve($root, $args)
    {

        if (Auth::user()->type != 'suporte') {
            return 'Apenas analistas podem tomar posse de atendimentos.';
        }

        $atendimento = Atendimento::find($args['atendimento_id']);

        $analistaId = Auth::id();
        $areaId = $atendimento->area_id;

        $user = User::findOrFail($analistaId);
        if (!$user->areas->contains($areaId)) {
            return 'Apenas analistas da area podem tomar posse de atendimentos.';
        }

        $atendimento->status = 'em andamento';
        $atendimento->analista_id = $analistaId;
        $atendimento->save();

        return "Atendimento em andamento após posse de {$user->name}.";
    }
}
