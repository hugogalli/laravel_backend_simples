<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AnalistaRelatorioType extends GraphQLType
{
    protected $attributes = [
        'name' => 'AnalistaRelatorio',
        'description' => 'Relatório de Analista',
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID do Analista',
            ],
            'nome' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Nome do Analista',
            ],
            'total_atendimentos' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Número de atendimentos',
            ],
            'total_atendimentos_concluidos' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Número de atendimentos',
            ],
        ];
    }
}
