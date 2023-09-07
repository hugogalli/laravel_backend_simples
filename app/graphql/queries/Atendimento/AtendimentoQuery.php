<?php

namespace App\GraphQL\Queries\Atendimento;

use App\Models\Atendimento;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class AtendimentoQuery extends Query
{
    protected $attributes = [
        'name' => 'atendimento',
    ];

    public function type(): Type
    {
        return GraphQL::type('Atendimento');
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::int(),
                'rules' => ['required']
            ]
        ];
    }
    
    public function resolve($root, $args)
    {
        return Atendimento::findOrFail($args['id']);
    }
}