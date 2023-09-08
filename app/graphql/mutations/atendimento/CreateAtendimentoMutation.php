<?php

namespace App\GraphQL\Mutations\Atendimento;

use App\Models\Atendimento;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\Auth;
use Rebing\GraphQL\Support\Facades\GraphQL;

class CreateAtendimentoMutation extends Mutation
{
    protected $attributes = [
        'title' => 'createAtendimento',
        'description' => 'Creates a new Atendimento'
    ];

    public function type(): Type
    {
        return GraphQL::type('Atendimento');
    }

    public function args(): array
    {
        return [
            'title' => [
                'name' => 'title',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required'],
            ],
            'tipo' => [
                'name' => 'tipo',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required','in:problema,duvida,solicitacao'],
            ],
            'description' => [
                'name' => 'description',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required'],
            ],
            'pessoa' => [
                'name' => 'pessoa',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required'],
            ],
            'cliente_id' => [
                'name' => 'cliente_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required'],
            ],
            'area_id' => [
                'name' => 'area_id',
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required'],
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $atendimento = new Atendimento();
        $atendimento->fill($args);

        $atendimento->user_id = Auth::id();
        $atendimento->status = 'pendente';

        $atendimento->save();

        return $atendimento;
    }
}
