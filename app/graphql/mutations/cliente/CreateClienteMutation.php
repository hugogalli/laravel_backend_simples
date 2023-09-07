<?php

namespace App\GraphQL\Mutations\Cliente;

use App\Models\Cliente;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class CreateClienteMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createCliente',
        'description' => 'Creates a new Cliente'
    ];

    public function type(): Type
    {
        return GraphQL::type('Cliente');
    }

    public function args(): array
    {
        return [
            'title' => [
                'name' => 'title',
                'type' =>  Type::nonNull(Type::string()),
            ],
        ];
    }

    public function resolve($root, $args)
    {
        $cliente = new Cliente();
        $cliente->fill($args);
        $cliente->save();

        return $cliente;
    }
}