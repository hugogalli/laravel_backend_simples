<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AreaRelatorioType extends GraphQLType
{
    protected $attributes = [
        'name' => 'AreaRelatorio',
        'description' => 'Relatório de areas',
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID da area',
            ],
            'title' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Titulo da area',
            ],
            'num_atendimentos' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Número de atendimentos',
            ],
        ];
    }
}
