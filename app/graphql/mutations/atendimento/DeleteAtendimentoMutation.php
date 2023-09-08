<?php

namespace App\GraphQL\Mutations\Atendimento;

use App\Models\Atendimento;
use App\Models\User;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;

class DeleteAtendimentoMutation extends Mutation
{
    protected $attributes = [
        'title' => 'deleteAtendimento',
        'description' => 'Deletar um atendimento'
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
        $atendimento = Atendimento::find($args['atendimento_id']);
        $atendimento->delete();

        return 'Atendimento deletado com sucesso';
    }
}
