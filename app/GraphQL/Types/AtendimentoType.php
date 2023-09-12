<?php

namespace App\GraphQL\Types;

use App\Models\Atendimento;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AtendimentoType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Atendimento',
        'description' => 'Collection of Atendimentos',
        'model' => Atendimento::class
    ];

    public function fields(): array
    {   
        // ['title', 'description', 'tipo', 'pessoa', 'user_id', 'cliente_id', 'area_id', 'analista_id', 'status', 'info_adicional'];
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID of Atendimento'
            ],
            'title' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Title of the Atendimento'
            ],
            'description' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Description of the Atendimento'
            ],
            'tipo' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Tipo of the Atendimento'
            ],
            'pessoa' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Pessoa que requisitou o Atendimento'
            ],
            'user_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID do Atendente que cadastrou o Atendimento'
            ],
            'atendente' => [
                'type' => GraphQL::type('User'), 
                'description' => 'Atendente que cadastrou o atendimento',
                'resolve' => function($atendimento) {
                    return $atendimento->user;
                },
            ],
            'cliente_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Cliente que requisitou o Atendimento'
            ],
            'cliente' => [
                'type' => GraphQL::type('Cliente'), 
                'description' => 'Cliente que cadastrou o atendimento',
                'resolve' => function($atendimento) {
                    return $atendimento->cliente;
                },
            ],
            'area_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Area do Atendimento'
            ],
            'area' => [
                'type' => GraphQL::type('Area'), 
                'description' => 'Area do atendimento',
                'resolve' => function($atendimento) {
                    return $atendimento->area;
                },
            ],
            'analista_id' => [
                'type' => Type::int(),
                'description' => 'Analista responsavel por resolver o Atendimento'
            ],
            'analista' => [
                'type' => GraphQL::type('User'), 
                'description' => 'Analista responsavel pelo atendimento',
                'resolve' => function($atendimento) {
                    return $atendimento->analista;
                },
            ],
            'status' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Status do Atendimento'
            ],
            'info_adicional' => [
                'type' => Type::string(),
                'description' => 'Informacao adicional sobre o Atendimento'
            ],
        ];
    }
}