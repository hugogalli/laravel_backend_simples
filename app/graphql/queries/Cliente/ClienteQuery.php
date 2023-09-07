<?php

namespace App\GraphQL\Queries\Cliente;

use App\Models\Cliente;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class ClienteQuery extends Query
{
    protected $attributes = [
        'name' => 'cliente',
    ];

    public function type(): Type
    {
        return GraphQL::type('Cliente');
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
        return Cliente::findOrFail($args['id']);
    }
}