<?php

namespace App\GraphQL\Mutations\Cliente;

use App\Models\Cliente;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;

class UpdateClienteMutation extends Mutation
{
    protected $attributes = [
        'name' => 'updateCliente',
        'description' => 'Updates an Cliente'
    ];

    public function type(): Type
    {
        //return GraphQL::type('Cliente');
        return Type::string();
    }

    public function args(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' =>  Type::nonNull(Type::int()),
            ],
            'title' => [
                'name' => 'title',
                'type' =>  Type::nonNull(Type::string()),
            ],
        ];
    }

    public function resolve($root, $args)
    {   
        try {
            $cliente = Cliente::findOrFail($args['id']);
            $cliente->fill($args);
            $cliente->save();
            return 'Cliente '. $cliente->id .' atualizado';
        } catch (\Exception $e) {
            return 'Cliente não encontrado';
        }

    }
}
