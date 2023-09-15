<?php

namespace App\GraphQL\Mutations\Atendimento;

use App\Models\Atendimento;
use App\Models\User;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ConcluirAtendimentoMutation extends Mutation
{
    protected $attributes = [
        'title' => 'concluirAtendimento',
        'description' => 'Analista conclui um atendimento'
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
            'info_adicional' => [
                'name' => 'info_adicional',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required'],
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $atendimento = Atendimento::find($args['atendimento_id']);
        $analistaId = Auth::id();

        if (Auth::user()->type != 'suporte') {
            throw new \Exception('Apenas suportes podem completar atendimentos!');
        }

        if ($analistaId != $atendimento->analista_id) {
            throw new \Exception('Você não é o dono desse atendimento!');
        }

        $atendimento->status = 'concluido';
        $atendimento->info_adicional = $args['info_adicional'];
        $atendimento->save();

        return 'Atendimento Finalizado';
    }
}
