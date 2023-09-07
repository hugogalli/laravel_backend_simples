<?php

namespace App\GraphQL\Queries\Atendimento;

use App\Models\Atendimento;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class AtendimentosQuery extends Query
{
    protected $attributes = [
        'name' => 'atendimentos',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Atendimento'));
    }

    public function resolve($root, $args)
    {
        return Atendimento::all();
    }
}