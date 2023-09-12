<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TipoAtendimentoRelatorioType extends GraphQLType
{
    protected $attributes = [
        'name' => 'TipoAtendimentoRelatorio',
        'description' => 'Relatório de Tipo',
    ];

    public function fields(): array
    {
        return [
            'tipo' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Tipo do Atendimento',
            ],
            'num_atendimentos' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Número de atendimentos',
            ],
        ];
    }
}
