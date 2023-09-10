<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ClienteRelatorioType extends GraphQLType
{
    protected $attributes = [
        'name' => 'ClienteRelatorio',
        'description' => 'Relatório de clientes',
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID do cliente',
            ],
            'title' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Titulo do cliente',
            ],
            'num_atendimentos' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Número de atendimentos',
            ],
        ];
    }
}
