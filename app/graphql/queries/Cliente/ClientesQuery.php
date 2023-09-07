<?php

namespace App\GraphQL\Queries\Cliente;

use App\Models\Cliente;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class ClientesQuery extends Query
{
    protected $attributes = [
        'name' => 'clientes',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('Cliente'));
    }

    public function resolve($root, $args)
    {
        return Cliente::all();
    }
}