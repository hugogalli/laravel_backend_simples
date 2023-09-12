<?php

namespace App\GraphQL\Types;

use App\Models\Cliente;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ClienteType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Cliente',
        'description' => 'Collection of Clientes',
        'model' => Cliente::class
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID of Cliente'
            ],
            'title' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Title of the Cliente'
            ]
        ];
    }
}