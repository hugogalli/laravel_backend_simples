<?php

namespace App\GraphQL\Mutations\Atendimento;

use App\Models\Atendimento;
use App\Models\User;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;

class TransferirPosseMutation extends Mutation
{
    protected $attributes = [
        'title' => 'transferirPosse',
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
            'analista_id' => [
                'name' => 'analista_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required'],
            ],
        ];
    }

    public function resolve($root, $args)
    {

        // Verifique se o usuário autenticado é um atendente
        if (Auth::user()->type != 'atendente') {
            return 'Apenas atendentes podem transferir atendimentos.';
        }

        $user = User::findOrFail($args['analista_id']);

        // Verifique se o usuário autenticado é um atendente
        if ($user->type != 'suporte') {
            return 'Usuarios do tipo ' . $user->type . ' não podem receber posse de atendimentos.';
        }

        $nome = $user->name;
        $atendimento = Atendimento::find($args['atendimento_id']);
        $areaId = $atendimento->area_id;


        if (!$user->areas->contains($areaId)) {
            return 'Apenas analistas da area podem receber posse de atendimentos.';
        }

        $atendimento->status = 'em andamento';
        $atendimento->analista_id = $args['analista_id'];
        $atendimento->save();

        return 'Atendimento transferido com sucesso para a posse do analista ' . $nome;
    }
}
